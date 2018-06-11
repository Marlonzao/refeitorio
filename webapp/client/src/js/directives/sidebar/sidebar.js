app.directive('sidebar', function(){
    return{
        restrict: 'E',
        templateUrl: 'directives/sidebar/sidebar.html',
        replace: true,
        scope: {
            user : '=user'
        },
        controller: ['$scope', '$rootScope','$timeout', function($scope, $rootScope, $timeout){
            $rootScope.$on('mainLoading', function(evt, data) {
                $scope.loaded = true;
                $scope.sideBar = [
                    {
                        name: 'Início',
                        route: 'Dashboard.Home',
                        icon: 'fa-home'
                    },
    
                    ($rootScope.selectedEnviroment.type == 'school') ? {
                        name:'Alunos', 
                        route: 'Dashboard.Persons', 
                        icon: 'fa-graduation-cap'
                    } : {
                        name:'Funcionários', 
                        route: 'Dashboard.Persons', 
                        icon: 'fa-users'                    
                    }, 
    
                    {
                        name:'Refeitório', 
                        route: 'Dashboard.Refeitorio', 
                        icon: 'fa-chart-line'
                    }
                ]
            });


        }]
    }
})