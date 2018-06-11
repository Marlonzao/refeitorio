app.directive('sidebar', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/sidebar/sidebar.html',
        replace: true,
        scope: {
            userData: '=userData'
        },
        controller: ['$scope', '$rootScope','personsFactory', 'nameWeekday', 'historyFactory', function($scope, $rootScope, personsFactory, nameWeekday, historyFactory){

        }]
    }
})