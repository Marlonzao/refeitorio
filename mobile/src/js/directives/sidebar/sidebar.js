app.directive('sidebar', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/sidebar/sidebar.html',
        replace: true,
        scope: {
            user : '=user'
        },
        controller: ['$scope', '$rootScope','$timeout', function($scope, $rootScope, $timeout){
            $timeout(function(){ $scope.loaded = true; }, 1500);
            $scope.sideBar = [
                {
                    name: 'Início',
                    route: 'Dashboard.Home',
                    icon: 'fa-home'
                },

                // ($rootScope.selectedEnviroment.type == 'school') ? {
                //     name:'Alunos', 
                //     route: 'Dashboard.Persons', 
                //     icon: 'fa-graduation-cap'
                // } : {
                //     name:'Funcionários', 
                //     route: 'Dashboard.Persons', 
                //     icon: 'fa-users'                    
                // }, 

                // {
                //     name:'Refeitório', 
                //     route: 'Dashboard.Refeitorio', 
                //     icon: 'fa-chart-line'
                // }
            ]
        }]
    }
})