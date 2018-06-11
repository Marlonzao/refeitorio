app.factory('userFactory', ['$http', 'apiURL', '$rootScope', function($http, apiURL, $rootScope) {
    var userFactory = {};
    var baseURL = apiURL.get()+'auth/'; 

    userFactory.loadData = function(){
        return $http.get(baseURL+'user').then(
            function(result){
                if(result.status==200 && !$rootScope.userData.name){
                    $rootScope.userData.name                = result.data.data.name;
                    $rootScope.userData.firstName           = result.data.data.name.split(' ').slice(0, 1).join(' ');
                    $rootScope.userData.email               = result.data.data.email;
                    // $rootScope.userData.role                = result.data.data.role;                    
                    $rootScope.userData.role                = result.data.data.role;                    
                    // TODO: Mover dados para banco de dados
                    $rootScope.userData.availableModules    = {0: true, 1: true, 2: true};
                    $rootScope.userData.bio = ''
                    $rootScope.userData.photo = 'https://qualiscare.com/wp-content/uploads/2017/08/default-user.png'
                }
    
                return true            
            }, function(result){
                return false
            }
        );    
    }

    userFactory.register = function(register){
        return $http.post(apiURL.get()+'register/user', register).then(function(result){
            if(result.data == 'success'){
                return true
            }
        }, function(result){
            return false
        })
    }

    return userFactory;
}]);