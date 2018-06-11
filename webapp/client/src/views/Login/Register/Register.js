app.controller('RegisterController', ['$scope', '$rootScope', 'userFactory', 'toaster', function($scope, $rootScope, userFactory, toaster){
    $scope.send = function(){
        if($scope.register.password != $scope.register.confirm_password){
            toaster.pop('error', 'Senhas diferentes', '');
            return
        }

        userFactory.register($scope.register).then(function(result){
            if(result){
                toaster.pop('success', 'Usuário cadastrado com sucesso', '');
            }
        });
    }
}]) 