app.factory('personsFactory', ['$http', 'apiURL', '$rootScope', 'toaster', function($http, apiURL, $rootScope, toaster) {
    var personsFactory = {};
    var baseURL = apiURL.get()+'person'; 
    
    personsFactory.get = function(personID){
        return $http.get(baseURL+'/'+personID).then( function(result){
            return result.data;
        }, function(result){
            return false
        })
    } 

    personsFactory.searchPerson = function(searchTerm){
        return $http.get(baseURL+'/'+$rootScope.selectedEnviroment.id+'/search/'+searchTerm).then( function(result){
            return result.data;
        }, function(result){
            return false
        })        
    }

    return personsFactory;
}]);