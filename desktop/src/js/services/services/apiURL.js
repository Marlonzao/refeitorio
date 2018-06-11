app.service('apiURL', ['$location', function($location) {
    this.get = function(){
        if ($location.host() === "localhost")
            return $location.protocol() + "://" + $location.host() + "/refeitorio/webapp/backend/public/api/";
        if($location.host().split('.')[1] === "ngrok")
            return $location.protocol() + "://" + $location.host() + "/refeitorio/webapp/backend/public/api/";
        if($location.host().split('.')[1] === "facepedidos")
            return $location.protocol() + "://" + $location.host() + "/server/api/"; 

        return "http://localhost/refeitorio/webapp/backend/public/api/";        
    };
}]);