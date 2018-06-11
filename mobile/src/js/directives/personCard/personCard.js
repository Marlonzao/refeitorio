app.directive('personCard', function(ngDialog){
    return{
        restrict: 'E',
        templateUrl: 'directives/personCard/personCard.html',
        replace: true,
        scope: {
            person: '=person'
        },
        controller: ['$scope', '$rootScope', 'ngDialog', 'toaster','personsFactory', 'nameWeekday', 'historyFactory', function($scope, $rootScope, ngDialog, toaster, personsFactory, nameWeekday, historyFactory){
            $scope.$watch('person', function() {
                $scope.allowed = false;
                $scope.processed = false;
                time = new Date().getHours();
                console.log('entrou');                                      
                
                if(time >= 11 && time < 14){
                    console.log('tempo certo: '+time);
                    if($scope.person.lunch_pattern[nameWeekday.get()]){
                        $scope.allowed = true;
                        console.log('via sala');
                    }
                    if($scope.person.isBoarder){
                        $scope.allowed = true;  
                        console.log('via interno');                                      
                    }
                }

                if(time >= 18 && time < 21){
                    console.log('tempo certo: '+time);
                    if($scope.person.dinner_pattern[nameWeekday.get()]){
                        $scope.allowed = true;
                        console.log('via sala');
                    }
                    if($scope.person.isBoarder){
                        $scope.allowed = true;  
                        console.log('via interno');                                      
                    }
                }
            });
            

            $scope.register = function(){
                if($scope.allowed){
                    $scope.allowed = false;
                    historyFactory.register($scope.person.id, 'byEnviroment', $rootScope.selectedEnviroment.id).then( function(result){
                        if(result){
                            swal('Feito!', 'Aluno autorizado com sucesso.', 'success')    
                        }
                    })
                }
            }

            $scope.money = function(){
                if(!$scope.processed){
                    $scope.processed = true;
                    historyFactory.register($scope.person.id, 'byPerson', $rootScope.selectedEnviroment.id).then( function(result){
                        if(result){
                            swal('Feito!', 'Pagamento processado com sucesso.', 'success')    
                        }
                    })
                }
            }
        }]
    }
})