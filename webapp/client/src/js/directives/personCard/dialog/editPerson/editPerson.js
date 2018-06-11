app.controller('EditPersonController', ['$scope', 'personID', 'personsFactory', function($scope, personID, personsFactory){
    personsFactory.get(personID).then( function(resultPerson){
        $scope.person = resultPerson;
        $scope.loaded = true;
    })

    $scope.send = function(person){
        personsFactory.editPerson(person).then(function(result){
            if(result){
                $scope.closeThisDialog();
            }
        });
    }
}])