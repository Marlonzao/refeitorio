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
        .state('Dashboard.Persons', {
            url: '/Persons',
            templateUrl: 'Dashboard/Persons/Persons.html',
            controller: 'PersonsController' 
        })
        .state('Dashboard.Refeitorio', {
            url: '/Refeitorio',
            templateUrl: 'Dashboard/Refeitorio/Refeitorio.html',
            controller: 'RefeitorioController' 
        })
});