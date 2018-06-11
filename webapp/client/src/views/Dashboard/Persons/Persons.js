app.controller('PersonsController', ['$scope', '$rootScope', 'toaster', 'personsFactory', 'fileUpload', function($scope, $rootScope, toaster, personsFactory, fileUpload){
    function updatePersons(){
        personsFactory.getMany(6).then(function(result){
            $scope.persons = result;
        });
    }

    updatePersons();

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

    $scope.uploadFile = function(){
        if(!$scope.myFile){
            swal('Nenhum arquivo selecionado!', '', 'error');
        }else{
            swal({
                title: 'Por favor aguarde',
                text: 'O arquivo está sendo processado, isto pode levar alguns minutos...',
                onOpen: function(){
                    swal.showLoading()
                }
            })
            fileUpload.uploadFileToUrl($scope.myFile).then( function(result){
                if(result){
                    updatePersons();
                    swal.close();
                    swal('Feito!', 'Alunos gerados com sucesso.', 'success')    
                }
            })

        }
    };


    $rootScope.$on('updatePersons', function(evt, data) {
        updatePersons();
    });

}]) 