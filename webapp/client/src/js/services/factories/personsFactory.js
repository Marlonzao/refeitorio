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

    personsFactory.editPerson = function(person){
        return $http.put(baseURL+'/'+person.id, person).then( function(result){
            toaster.pop('success', "Editado", "aluno editado com sucesso");
            return true
        }, function(result){
            toaster.pop('error', "Falha ao editar aluno", "Por favor tente novamente mais tarde");
            return false
        })
    }

    personsFactory.filePerson = function(studentID){
        studentID = (studentID instanceof Array) ? studentID : [studentID];
        return $http.post(baseURL+'/file', studentID).then( function(result){
            toaster.pop('success', "Arquivado", "aluno arquivado com sucesso");
            return true
        }, function(result){
            toaster.pop('error', "Falha ao arquivar aluno", "Por favor tente novamente mais tarde");
            return false
        })
    }

    personsFactory.deletePerson = function(personID){
        personID = (personID instanceof Array) ? personID : [personID];        
        return $http.post(baseURL+'/delete', personID).then( function(result){
            toaster.pop('success', "Deletado", "aluno deletado com sucesso");
            return true
        }, function(result){
            toaster.pop('error', "Falha ao deletar aluno", "Por favor tente novamente mais tarde");
            return false
        })
    }

    return personsFactory;
}]);