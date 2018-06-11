app.directive('personCard', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/personCard/personCard.html',
        replace: true,
        scope: {
            person: '=person'
        },
        controller: ['$scope', '$rootScope','personsFactory', 'nameWeekday', 'historyFactory', function($scope, $rootScope, personsFactory, nameWeekday, historyFactory){
            $scope.$watch('person', function() {
                $scope.allowed      = false;
                $scope.notProcessed = false;
                time = new Date().getHours();
                
                if(time >= 11 && time < 14){
                    if($scope.person.lunch_pattern[nameWeekday.get()]){
                        $scope.allowed = true;
                    }
                    if($scope.person.isBoarder){
                        $scope.allowed = true;  
                    }
                }

                if(time >= 18 && time < 21){
                    if($scope.person.isBoarder){
                        $scope.allowed = true;  
                    }
                }

                historyFactory.check($scope.person.id).then( function(result){
                    if(!result){
                        $scope.conclict = true;
                    }
                    $scope.loaded = true;
                })
            });

            $scope.register = function(){
                if($scope.allowed){
                    $scope.allowed = false;
                    historyFactory.register($scope.person.id, 'byEnviroment').then( function(result){
                        if(result){
                            swal('Feito!', 'Aluno autorizado com sucesso.', 'success')    
                        }
                    })
                }
            }

            $scope.money = function(){
                if(!$scope.notProcessed){
                    $scope.notProcessed = true;
                    historyFactory.register($scope.person.id, 'byPerson').then( function(result){
                        if(result){
                            swal('Feito!', 'Pagamento processado com sucesso.', 'success')    
                        }
                    })
                }
            }
        }]
    }
})