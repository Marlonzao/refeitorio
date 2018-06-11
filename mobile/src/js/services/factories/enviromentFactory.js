app.factory('enviromentFactory', ['$http', 'apiURL', '$rootScope', function($http, apiURL, $rootScope) {
    var enviromentFactory = {};
    var baseURL = apiURL.get()+'enviroment/';

    enviromentFactory.getAll = function(){
        return $http.get(baseURL+'all').then( function(result){
            $rootScope.enviroments = result.data;
            return true;
        }, function(){
            return false;
        });
    }

    return enviromentFactory;
}]);