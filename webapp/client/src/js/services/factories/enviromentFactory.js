app.factory('enviromentFactory', ['$http', 'apiURL', '$rootScope', function($http, apiURL, $rootScope) {
    var enviromentFactory = {};
    var baseURL = apiURL.get()+'enviroment/';

    enviromentFactory.getAll = function(){
        return $http.get(baseURL+'all').then( function(result){
            $rootScope.enviroments = result.data;
            $rootScope.selectedEnviroment = result.data[0];
            return true;
        }, function(){
            return false;
        });
    }

    enviromentFactory.register = function(enviroment){
        return $http.post(baseURL+'register', enviroment).then( function(result){
            enviromentFactory.getAll();
            return true;
        }, function(){
            return false;
        })
    }

    return enviromentFactory;
}]);