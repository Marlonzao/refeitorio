app.controller('DashboardController', ['$scope', '$state', '$rootScope', 'userFactory', 'reportFactory', 'personsFactory', 'enviromentFactory', 'historyFactory', 'jwtHelper', function($scope, $state, $rootScope, userFactory, reportFactory, personsFactory, enviromentFactory, historyFactory, jwtHelper){
    $rootScope.$on('mainLoading', function(evt, data) {
        $scope.loaded = true;
    });

    userFactory.loadData().then(function(result){
        if(result){
            enviromentFactory.getAll().then( function(result){
                if(result){
                    $rootScope.$broadcast('mainLoading', true);
                }
            });
        }else{
        }
    });

    $state.go('Dashboard.Home');    
}]) 