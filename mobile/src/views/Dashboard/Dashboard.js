app.controller('DashboardController', ['$scope', '$state', '$rootScope', 'userFactory', 'toaster', 'enviromentFactory', function($scope, $state, $rootScope, userFactory, toaster, enviromentFactory){
    userFactory.loadData().then(function(result){
        if(result){
            enviromentFactory.getAll().then(function(result){if(result){
                $scope.loaded = true;
                console.log($rootScope.enviroments);
            }else{
                toaster.pop('error', "Falha", "Falha ao coletar dados, verifique sua conexão ou tente novamente mais tarde");                                
            }});
        }else{
            toaster.pop('error', "Falha", "Falha ao coletar dados, verifique sua conexão ou tente novamente mais tarde");        
        }
    });

    $scope.sidebarToggle = function(){
        angular.element(document.querySelector('#body')).toggleClass('sidenav-toggled');        
    }

    $state.go('Dashboard.Home');    
}]) 