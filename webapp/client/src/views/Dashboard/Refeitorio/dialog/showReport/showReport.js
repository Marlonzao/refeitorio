app.controller('ShowReportController', ['$scope', 'reportFather', 'reportFactory', function($scope, reportFather, reportFactory){
    $scope.reportFather = reportFather;
    $scope.getReports = function(page){
        $scope.loaded = false;
        page = typeof page !== 'undefined' ? page : 0;
        reportFactory.showReport(reportFather.id, page).then( function(result){
            $scope.loaded = true;
            $scope.reports = result.reports;
            $scope.noOfPages = result.totalCount;
        })
    }

    $scope.getReports();
}])