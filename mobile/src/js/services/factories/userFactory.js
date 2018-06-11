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
                    // TODO: Mover dados para banco de dados
                    $rootScope.userData.availableModules    = {0: true, 1: true, 2: true, 3: false, 4: false, 5: true};
                    $rootScope.userData.bio = 'Programador Ninja'
                    $rootScope.userData.photo = 'https://s3.amazonaws.com/uifaces/faces/twitter/jsa/48.jpg'
                }
    
                return true            
            }, function(result){
                return false
            }
        );    
    }

    return userFactory;
}]);