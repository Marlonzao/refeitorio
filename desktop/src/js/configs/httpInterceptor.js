app.service('authInterceptor',['$q', '$injector', '$state', function($q, $injector, $state) {
    var service = this;
    service.responseError = function(response) {
        if (response.status == 401 && $state.current.name != 'Login'){
            // var ngDialog = $injector.get("ngDialog");
            // ngDialog.closeAll();
            // swal({
            //     title: 'Faça login',
            //     text: "Você deve estar logado para ver esta página",
            //     type: 'error',
            //     confirmButtonText: 'Ok'
            // }).then(function () {
            //     $state.go('Login');                
            // },  function (dismiss) {
            //     $state.go('Login');                
            // })
        }
        return $q.reject(response);
    };
}]).config(['$httpProvider', function($httpProvider) {
    $httpProvider.interceptors.push('authInterceptor');
    $httpProvider.defaults.useXDomain = true;
    delete $httpProvider.defaults.headers.common['If-Modified-Since'];
}])
