app.controller('CreateEnviromentController', ['$scope', '$rootScope', 'enviromentFactory', 'toaster', function($scope, $rootScope, enviromentFactory, toaster){
    $scope.send = function(){
        enviromentFactory.register($scope.enviroment).then(function(result){
            if(result){
                toaster.pop('success', 'Ambiente cadastrado com sucesso', '');
                $scope.closeThisDialog();
            }
        });
    }
}])     