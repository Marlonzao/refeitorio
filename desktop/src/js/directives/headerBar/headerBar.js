app.directive('headerBar', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/headerBar/headerBar.html',
        replace: true,
        controller: ['$scope', '$rootScope','loginFactory', '$state', function($scope, $rootScope, loginFactory, $state){
            $scope.logout = function(){
                loginFactory.logout().then( function(result){
                    $state.go('Login');    
                });                
            }
        }]
    }
})