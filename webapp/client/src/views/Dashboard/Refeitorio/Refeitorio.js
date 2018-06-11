app.controller('RefeitorioController', ['$scope', '$rootScope', 'reportFactory', 'toaster', 'ngDialog', function ($scope, $rootScope, reportFactory, toaster, ngDialog, NgTableParams) {
    $scope.loadedDaily = false;
    $scope.loadedMonthly = false;
    
    $scope.showReport = function(reportFather){
        ngDialog.open({
            template: 'Dashboard/Refeitorio/dialog/showReport/showReport.html',
            className: 'ngdialog-theme-default',
            controller: 'ShowReportController',
            width: '1400px',
            resolve: {
                'reportFather': function(){
                    return reportFather;
                }
            }
        });
    }

    $scope.getPersonsDaily = function(page){
        $scope.loadedDaily = false;
        page = typeof page !== 'undefined' ? page : 0;
        reportFactory.getHistorics(page, 10).then( function(result){
            if(result){
                $scope.historicsDaily = result.historics;
                $scope.noOfPagesDaily = result.totalCount;
                $scope.loadedDaily = true;           
            }
        });
    }

    $scope.getPersonsDaily();

    $scope.getHistoricsMonthly = function(page){
        $scope.loadedMonthly = false;
        page = typeof page !== 'undefined' ? page : 0;
        reportFactory.getHistoricsMonthly(page, 10).then( function(result){
            if(result){
                $scope.historicsMonthly = result.historics;
                $scope.noOfPagesMonthly = result.totalCount;
                $scope.loadedMonthly = true;           
            }
        });
    }

    $scope.getHistoricsMonthly();

    $scope.downloadMonthReport = function(month, type){
        type = typeof type !== 'undefined' ? type : 'PDF';        
        swal({
            title: 'Por favor aguarde',
            text: 'Seu arquivo está sendo gerado, isto pode levar alguns minutos...',
            onOpen: function(){
                swal.showLoading()
            }
        })
        reportFactory.downloadMonthReport(month, type);
    }
}]) 