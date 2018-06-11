app.factory('historyFactory', ['$http', 'apiURL', function($http, apiURL) {
    var historyFactory = {};
    var baseURL = apiURL.get()+'history/';

    historyFactory.register = function(personID, paymentType, enviromentID){
        return $http.get(baseURL+'person/'+personID+'/'+paymentType+'/'+enviromentID).then(function(result){
            return true;    
        }, function(result){
            return false
        });
    }


    return historyFactory;
}]);