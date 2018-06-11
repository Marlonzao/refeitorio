app.factory('reportFactory', ['$http', 'apiURL', '$rootScope', 'toaster', 'moment', function($http, apiURL, $rootScope, toaster, moment) {
    var reportFactory = {};
    var baseURL = apiURL.get()+'report'; 
    
    reportFactory.getHistorics = function(offset, limit){
        return $http.get(baseURL+'/enviroment/'+$rootScope.selectedEnviroment.id+'/total/'+offset+'/'+limit).then( function(result){
            return result.data;
        }, function(result){
            return false
        })
    }    

    reportFactory.getChartHistorics = function(){
        return $http.get(baseURL+'/enviroment/'+$rootScope.selectedEnviroment.id+'/chart/week').then( function(result){
            return result.data;
        }, function(result){
            return false
        })
    }    

    reportFactory.getHistoricsMonthly = function(offset, limit){
        return $http.get(baseURL+'/enviroment/'+$rootScope.selectedEnviroment.id+'/monthly/'+offset+'/'+limit).then( function(result){
            return result.data;
        }, function(result){
            return false
        })
    }

    reportFactory.showReport = function(historicID, page){
        return $http.get(baseURL+'/'+historicID+'/'+page+'/10').then( function(result){
            result.data.reports.forEach(function(report, index){
                result.data.reports[index].created_at = new Date(report.created_at).toISOString();
            });

            return result.data;
        }, function(result){
            return false
        })
    }    

    reportFactory.downloadMonthReport = function(month, type){
        return $http.get(baseURL+'/enviroment/'+$rootScope.selectedEnviroment.id+'/download/monthly/'+month.replace("/", "-")+'/type/'+type, {responseType: 'arraybuffer'}).then( function(result){
            swal.close();
            swal('Feito!', 'Documento Gerado.', 'success')
            if(type == 'PDF'){
                var file = new Blob([result.data], {type: 'application/pdf'});
                var fileURL = URL.createObjectURL(file);
                window.open(fileURL);
            // }
            }else if(type == 'XLSX'){

                var blob = new Blob([result.data], { type: 'application/vnd.ms-excel' });
                var a = window.document.createElement("a");
                a.href = window.URL.createObjectURL(blob, { type: "application/vnd.ms-excel" });
                a.download = "relatorio_mes_" + moment().format('MM[-]YYYY') + ".xlsx";
                document.body.appendChild(a);
                a.click();  // IE: "Access is denied"; see: https://connect.microsoft.com/IE/feedback/details/797361/ie-10-treats-blob-url-as-cross-origin-and-denies-access
                document.body.removeChild(a);
            }

        }, function(result){
            return false
        })        
    }

    return reportFactory;
}]);