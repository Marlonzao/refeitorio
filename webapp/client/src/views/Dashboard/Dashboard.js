app.controller('DashboardController', ['$scope', '$state', '$rootScope', 'userFactory', 'toaster', 'reportFactory', 'personsFactory', 'enviromentFactory', 'ngDialog', 'historyFactory', 'jwtHelper', '$http',function($scope, $state, $rootScope, userFactory, toaster, Factory, personsFactory, enviromentFactory, ngDialog, historyFactory, jwtHelper, $http){
    $rootScope.$on('mainLoading', function(evt, data) {
        $scope.loaded = true;
    });
    
    // historyFactory.setSocket();

    userFactory.loadData().then(function(result){
        if(result){
            enviromentFactory.getAll().then( function(result){
                if(result){
                    $rootScope.$broadcast('mainLoading', true);
                }else{
                    ngDialog.open({
                        template: 'Dashboard/CreateEnviroment/CreateEnviroment.html',
                        className: 'ngdialog-theme-default',
                        controller: 'CreateEnviromentController',
                        closeByDocument: false,
                        closeByNavigation: false,
                        closeByEscape: false,
                        showClose: false,
                        width: '700px',
                    }).closePromise.then(function (data) {
                        $rootScope.$broadcast('mainLoading', true);
                    });
                }
            });
        }else{
            toaster.pop('error', "Falha", "Falha ao coletar dados, verifique sua conexão ou tente novamente mais tarde");        
        }
    });

    $scope.sidebarToggle = function(){
        angular.element(document.querySelector('#body')).toggleClass('sidenav-toggled');        
    }

    $state.go('Dashboard.Home');    
}]) 