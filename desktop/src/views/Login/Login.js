app.controller('LoginController', ['$scope', '$rootScope', 'loginFactory', 'userFactory', '$state', '$timeout', 'apiURL', 'toaster', function ($scope, $rootScope, loginFactory, userFactory, $state, $timeout, apiURL, toaster) {
    userFactory.loadData().then(
        function (result) {
            if (result) {
                $state.go('Dashboard');
            } else {
                $scope.loaded = true;
            }
        }
    );

    $scope.login = function () {
        swal({
            title: 'Por favor aguarde',
            text: 'Verificando usuário...',
            onOpen: function(){
                swal.showLoading()
            }
        })
        loginFactory.login($scope.email, $scope.password).then(function (result) {
            swal.close();
            if(result){
                $state.go('Dashboard');
                toaster.pop('success', "Sucesso", "Logado com sucesso");
            } else {
                swal('Erro', 'Credenciais inválidas.', 'error')
            }
        });
    }
}]) 