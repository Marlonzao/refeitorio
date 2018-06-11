app.factory('personsFactory', ['$http', 'apiURL', '$rootScope', function($http, apiURL, $rootScope) {
    var personsFactory = {};
    var baseURL = apiURL.get()+'person'; 
    
    personsFactory.get = function(personID){
        return $http.get(baseURL+'/'+personID).then( function(result){
            return result.data;
        }, function(result){
            return false
        })
    }    

    personsFactory.getMany = function(quantity){
        return $http.get(baseURL+'/'+$rootScope.selectedEnviroment.id+'/get/'+quantity).then( function(result){
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

    personsFactory.addPerson = function(person){
        return $http.post(baseURL, person).then( function(result){
            return true
        }, function(result){
            return false
        })
    }

    return personsFactory;
}]);