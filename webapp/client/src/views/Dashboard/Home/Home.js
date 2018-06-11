app.controller('HomeController', ['$scope', 'reportFactory', '$rootScope', function($scope, reportFactory, $rootScope){
    $loadedWeek = false;
    $scope.colors = ['#45b7cd', '#ff6384', '#ff8e72'];
    $scope.weekLabels = ['Domingo', 'Segunda-Feira', 'Terça-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'Sábado'];
    
    function loadCharts(){
        if($rootScope.selectedEnviroment.id){
            reportFactory.getChartHistorics().then(function(result){
                $scope.loadedWeek = true
                $scope.dataWeek = result;
            });
        }
    }

    loadCharts();

    $rootScope.$on('mainLoading', function(evt, data) {  
        loadCharts();
    })

    $scope.datasetOverrideWeek = [
        {
            label: "Montante",
            borderWidth: 1,
            type: 'bar'
        },
        {
            label: "Alunos",
            borderWidth: 3,
            hoverBackgroundColor: "rgba(255,99,132,0.4)",
            hoverBorderColor: "rgba(255,99,132,1)",
            type: 'line'
        }
    ];
}])