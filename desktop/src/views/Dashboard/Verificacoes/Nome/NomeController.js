app.controller('NomeController', ['$scope', '$rootScope', 'personsFactory',function($scope, $rootScope, personsFactory){
    function updatePersons(){
        personsFactory.getMany(6).then(function(result){
            $rootScope.persons = result;
        });
    }

    if($rootScope.persons === undefined || $rootScope.persons === null){
        updatePersons();
    }

    $scope.searchPerson = function(searchTerm){
        if(searchTerm){
            personsFactory.searchPerson(searchTerm).then(function(result){
                if(result){
                    $scope.resultPersons = result;
                }else{
                    toaster.pop('error', 'Nada encontrado', 'Nenhum aluno encontrado');                
                }
            });    
        }else{
            $scope.resultPersons = null;
            updatePersons();
        }
    }
}])