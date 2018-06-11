app.directive('userButton', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/userButton/userButton.html',
        replace: true,
        controller: ['$scope', '$state', 'loginFactory', 'toaster', function($scope, $state, loginFactory, toaster){
            $scope.logout = function(){
                loginFactory.logout().then( function(result){
                    toaster.pop('success', "Deslogado", "");
                    $state.go('Login');    
                });                
            }
        }]
    }
})