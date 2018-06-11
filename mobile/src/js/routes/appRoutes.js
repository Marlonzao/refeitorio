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
});