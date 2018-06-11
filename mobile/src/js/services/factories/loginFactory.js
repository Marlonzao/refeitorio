app.factory('loginFactory', ['$http', 'apiURL', function($http, apiURL) {
    var loginFactory = {};
    var baseURL = apiURL.get()+'auth/';

    loginFactory.login = function(email, password){
        localStorage.removeItem("id_token");
        return $http.post(baseURL+'login', {'email': email, 'password': password}).then(
            function(result){
                localStorage.setItem("id_token", result.data.data.token);
                $http.defaults.headers.common['Authorization'] = "Bearer "+result.data.data.token;
                return true    
            }, function(result){
                return false
            }
        );
    }
    loginFactory.logout = function(){
        return $http.delete(baseURL+'invalidate').then( 
            function(result){
                localStorage.removeItem("id_token");
                return true;                       
            }, function(result){
                localStorage.removeItem("id_token");
                return true;                
            }
        );
    }

    return loginFactory;
}]);