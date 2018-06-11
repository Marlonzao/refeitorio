app.directive('notificationMenu', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/notificationMenu/notificationMenu.html',
        replace: true,
        controller: ['$scope', '$rootScope', function($scope, $rootScope){
            $scope.alert = function(){
                alert('test');
            }
        }]
    }
})