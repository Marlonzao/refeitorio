app.factory('reportFactory', ['$http', 'apiURL', '$rootScope', function($http, apiURL, $rootScope) {
    var reportFactory = {};
    var baseURL = apiURL.get()+'report'; 

    return reportFactory;
}]);