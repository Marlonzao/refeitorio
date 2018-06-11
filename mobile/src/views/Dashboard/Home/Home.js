app.controller('HomeController', ['$scope', 'personsFactory', 'toaster', '$location', function ($scope, personsFactory, toaster, $location) {
    $scope.loaded = true;
    $scope.scan = function () {
        $scope.loaded = false;
        if($location.host() === "localhost") {
            personsFactory.get(1).then(function (result) {
                if (result) {
                    $scope.loaded = true
                    $scope.person = result;
                } else {
                    toaster.pop('error', "Falha ao verificar pessoa", "verifique a conexão");
                }
            });
        } else {
            cordova.plugins.barcodeScanner.scan(function (result) {
                if (!result.cancelled && result.format == "QR_CODE") {
                    personsFactory.get(result.text).then(function (result) {
                        if (result) {
                            $scope.loaded = true
                            $scope.person = result;
                        } else {
                            toaster.pop('error', "Falha ao verificar pessoa", "verifique a conexão");
                        }
                    });
                }
            }, function (error) {
                alert("Scanning failed: " + error);
            }); 
        }
    }
}])