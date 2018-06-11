app.service('fileUpload', ['$http', 'apiURL', '$rootScope', function ($http, apiURL, $rootScope) {
    this.uploadFileToUrl = function (file) {
        var fd = new FormData();
        fd.append('file', file);

        return $http.post(apiURL.get()+'person/import/enviroment/'+$rootScope.selectedEnviroment.id, fd, {
            transformRequest: angular.identity,
            headers: { 'Content-Type': undefined }
        }).then( function(){
            return true
        }, function(){
            return false
        });
    }
}]);