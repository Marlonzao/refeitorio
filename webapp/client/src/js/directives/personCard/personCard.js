app.directive('personCard', function(ngDialog){
    return{
        restrict: 'E',
        templateUrl: 'directives/personCard/personCard.html',
        replace: true,
        scope: {
            person: '=person'
        },
        controller: ['$scope', '$rootScope', 'ngDialog', 'toaster','personsFactory', 'nameWeekday', 'historyFactory', function($scope, $rootScope, ngDialog, toaster, personsFactory, nameWeekday, historyFactory){
            $scope.user = $rootScope.userData;
            $scope.$watch('person', function() {
                $scope.allowed = false;
                time = new Date().getHours();
                // $scope.processed = false;
                
                checker = historyFactory.check($scope.person.id).then(function(result){
                    return result;
                })

                checker.then(function(conflict){
                    $scope.loaded = true;
                
                    if(!(time >= 11 && time < 14) && !(time >= 18 && time < 21)){
                        $scope.outTime = true
                        return
                    }

                    if($scope.person.student.isBoarder){
                        if((time >= 11 && time < 14) && conflict.noon){
                            $scope.conflict = true;
                            $scope.allowed = false;
                            return    
                        }else if((time >= 18 && time < 21) && conflict.late){
                            $scope.conflict = true;
                            $scope.allowed = false;
                            return
                        }
                    }else if((time >= 11 && time < 14) && conflict.noon){
                        $scope.conflict = true;
                        $scope.allowed = false;
                        return
                    }

                    if(time >= 11 && time < 14){
                        if($scope.person.lunch_pattern[nameWeekday.get()] || $scope.person.student.isBoarder){
                            $scope.allowed = true;
                            return
                        }else if(!$scope.person.lunch_pattern[nameWeekday.get()] || !$scope.person.student.isBoarder){
                            $scope.noRights = true;
                            $scope.allowed = false;
                            return
                        }
                    }

                    if(time >= 18 && time < 21){
                        if($scope.person.student.isBoarder){
                            $scope.allowed = true;  
                            return
                        }else{
                            $scope.noRights = true;
                            $scope.allowed = false;
                            return                            
                        }
                    }
                })
            });

            $scope.register = function(){
                if($scope.allowed){
                    $scope.allowed = false;
                    historyFactory.register($scope.person.id, 'byEnviroment').then( function(result){
                        if(result){
                            toaster.pop('success', "Feito!", "Aluno autorizado com sucesso.");                
                            // swal('Feito!', 'Aluno autorizado com sucesso.', 'success')    
                        }else{
                            swal('Erro', 'Ocorreu um erro ao aprovar o aluno, por favor anote o nome do mesmo e informe o administrador.', 'error')                                
                        }
                    })
                }
            }

            // $scope.money = function(){
            //     if(!$scope.processed){
            //         $scope.processed = true;
            //         historyFactory.register($scope.person.id, 'byPerson').then( function(result){
            //             if(result){
            //                 swal('Feito!', 'Pagamento processado com sucesso.', 'success')    
            //             }
            //         })
            //     }
            // }

            $scope.deletePerson = function(personID){
                swal({
                    title: 'Tem certeza?',
                    text: "O aluno vai ser deletado!",
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Sim',
                    allowOutsideClick: false                    
                }).then(function(result){
                    if(result.value){
                        personsFactory.deletePerson(personID).then( function(result){
                            if(result){
                                $rootScope.$broadcast('updatePersons', true);
                            }
                        });
                    }
                })
            }

            $scope.editPerson = function(personID){
                ngDialog.open({
                    template: 'directives/personCard/dialog/editPerson/editPerson.html',
                    className: 'ngdialog-theme-default',
                    controller: 'EditPersonController',
                    width: '700px',
                    resolve: {
                        'personID': function(){
                            return personID;
                        }
                    }
                }).closePromise.then(function (data) {
                    if (data.value && data.value != '$document') {
                        $rootScope.$broadcast('updatePersons', true);
                    }
                });
            }
        }]
    }
})