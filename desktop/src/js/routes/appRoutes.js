app.config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/Login');
    
    $stateProvider
        .state('Login', {
            url: '/Login',
            templateUrl: 'Login/Login.html',
            controller: 'LoginController' 
        })
        .state('Dashboard', {
            url: '/Dashboard',
            templateUrl: 'Dashboard/Dashboard.html',
            controller: 'DashboardController'
        })
        .state('Dashboard.Home', {
            url: '/Home',
            templateUrl: 'Dashboard/Home/Home.html',
            controller: 'HomeController'
        })
        .state('Dashboard.Nome', {
            url: '/Nome',
            templateUrl: 'Dashboard/Verificacoes/Nome/Nome.html',
            controller: 'NomeController'
        })
        .state('Dashboard.Subsidiado', {
            url: '/Subsidiado',
            templateUrl: 'Dashboard/Verificacoes/Subsidiado/Subsidiado.html',
            controller: 'HomeController'
        })
        .state('Dashboard.Dinheiro', {
            url: '/Dinheiro',
            templateUrl: 'Dashboard/Verificacoes/Dinheiro/Dinheiro.html',
            controller: 'HomeController'
        })
});