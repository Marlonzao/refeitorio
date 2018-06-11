app.factory('historyFactory', ['$http', 'apiURL','$rootScope', '$location',  function($http, apiURL, $rootScope, $location) {
    var historyFactory = {};
    var baseURL = apiURL.get()+'history/';

    // socket = socketFactory({
    //     ioSocket: io.connect($location.host()+':3002')
    // });

    historyFactory.register = function(personID, paymentType){
        // socket.emit('history', {'personID': personID, 'paymentType': paymentType});
        return $http.get(baseURL+'person/'+personID+'/'+paymentType+'/'+$rootScope.selectedEnviroment.id).then(function(result){
            return true;    
        }, function(result){
            return false
        });
    }

    historyFactory.check = function(personID){
        return $http.get(baseURL+'check/person/'+personID).then(function(result){
            return true;    
        }, function(result){
            return false
        });        
    }

    // historyFactory.setSocket = function(){
    //     socket.emit('history', {type:'registerEnviroment', enviromentID:$rootScope.selectedEnviroment.id});
    // }


    // historyFactory.setSocket = function(){
    //     // registryFactory.removeListener('history');
    //     socket.emit('history', {type:'registerEnviroment', enviromentID:$rootScope.selectedEnviroment.id});
    //     socket.on( 'history', function(data){
    //         $rootScope.historyRecords.push(JSON.parse(angular.toJson(history)));
    //         console.log($rootScope.historyRecords);
    //     });    
    // }

    return historyFactory;
}]);