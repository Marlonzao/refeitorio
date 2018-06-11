<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$api = $app->make(Dingo\Api\Routing\Router::class);
$api->version('v1', function ($api) {

    $api->get('/test', function(){
        return('test');
    });

    $api->group(['middleware' => "checkRole:user,admin"], function ($api) {

    });

    $api->get('/test/middleware', ['middleware' => "checkRole:user,admin"], function(){
        return('test');        
    });

    //------------------------------------- Enviroments Related
    
    $api->group(['prefix'=>'enviroment','middleware' => 'api.auth|checkRole:user,admin'], function ($api) {
        $api->get('/all', [
            'uses' => 'App\Http\Controllers\EnviromentsController@getAll',
            'as' => 'api.enviroments.getAll'
        ]);
        $api->post('/register', [
            'uses' => 'App\Http\Controllers\EnviromentsController@register',
            'as' => 'api.enviroments.register'
        ]);
    });

    //------------------------------------- History Related

    $api->group(['prefix'=>'history','middleware' => 'api.auth|checkRole:user,admin'], function ($api) {
        $api->get('/person/{personID}/{paymentType}/{enviromentID}',  [
            'uses' => 'App\Http\Controllers\HistoryController@register',
            'as' => 'api.history.register'
        ]);
        $api->get('/check/person/{personID}', [
            'uses' => 'App\Http\Controllers\HistoryController@checkHistoryMirror',
            'as' => 'api.history.check'
        ]);
        // $api->get('enviroment/{enviromentID}/total/{offset}/{limit}', [
        //     'uses' => 'App\Http\Controllers\ReportController@getAllTotal',
        //     'as' => 'api.report.getAllTotal'
        // ]);
    });

    //------------------------------------- Report Related

    $api->group(['prefix'=>'report','middleware' => 'api.auth'], function ($api) {
        $api->get('/{date}/total', [
            'uses' => 'App\Http\Controllers\ReportController@getTotalByDate',
            'as' => 'api.report.getTotalByDate'
        ]);
        $api->get('enviroment/{enviromentID}/total/{offset}/{limit}', [
            'uses' => 'App\Http\Controllers\ReportController@getAllTotal',
            'as' => 'api.report.getAllTotal'
        ]);
        $api->get('enviroment/{enviromentID}/monthly/{offset}/{limit}', [
            'uses' => 'App\Http\Controllers\ReportController@generateMonthlyReports',
            'as' => 'api.report.generateMonthlyReports'
        ]);
        $api->get('enviroment/{enviromentID}/chart/week', [
            'uses' => 'App\Http\Controllers\ReportController@generateChartWeekReport',
            'as' => 'api.report.generateChartWeekReport'
        ]);
        $api->get('{historicID}/{offset}/{limit}', [
            'uses' => 'App\Http\Controllers\ReportController@generateReport',
            'as' => 'api.report.generateReport'
        ]);
        $api->get('enviroment/{enviromentID}/download/monthly/{date}/type/{type}', [
            'uses' => 'App\Http\Controllers\ReportController@downloadMonthlyReport',
            'as' => 'api.report.downloadMonthlyReport'
        ]);        
        // $api->get('/{studentID}/student', [
        //     'uses' => 'App\Http\Controllers\ReportController@getByStudent',
        //     'as' => 'api.report.getByStudent'
        // ]);
        // $api->get('/{date}/{format}/day/download', [
        //     'uses' => 'App\Http\Controllers\ReportController@downloadDay',
        //     'as' => 'api.report.downloadDay'
        // ]);
        // $api->get('/{date}/{format}/month/download', [
        //     'uses' => 'App\Http\Controllers\ReportController@downloadMonth',
        //     'as' => 'api.report.downloadMonth'
        // ]);
    });

    //------------------------------------- Person Related

    $api->group(['prefix'=>'person','middleware' => 'api.auth'], function ($api) {
        $api->group(['middleware' => 'checkRole:staff,admin'], function ($api) {
            $api->post('/import/enviroment/{enviromentID}', [
                'uses' => 'App\Http\Controllers\PersonController@import',
                'as' => 'api.person.import'
            ]);
            $api->put('/{id}', [
                'uses' => 'App\Http\Controllers\PersonController@editPerson',
                'as' => 'api.person.edit'
            ]);
            $api->post('/delete', [
                'uses' => 'App\Http\Controllers\PersonController@deletePerson',
                'as' => 'api.person.delete'
            ]);
        });

        $api->get('/{id}', [
            'uses' => 'App\Http\Controllers\PersonController@getByID',
            'as' => 'api.person.getByID'
        ]);
        $api->get('/{enviromentID}/get/{quantity}', [
            'uses' => 'App\Http\Controllers\PersonController@getMany',
            'as' => 'api.person.getMany'
        ]); 
        $api->get('/{enviromentID}/search/{searchTerm}', [
            'uses' => 'App\Http\Controllers\PersonController@search',
            'as' => 'api.person.search'
        ]);
    });

    //------------------------------------- Auth Related

    $api->post('/auth/login', [
        'uses' => 'App\Http\Controllers\Auth\AuthController@postLogin',
        'as' => 'api.auth.login',
    ]);

    $api->post('/register/user', [
        'uses' => 'App\Http\Controllers\Auth\AuthController@registerUser',
        'as' => 'api.register.user',
    ]);

    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('/auth/user', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@getUser',
            'as' => 'api.auth.user'
        ]);
        $api->patch('/auth/refresh', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@patchRefresh',
            'as' => 'api.auth.refresh'
        ]);
        $api->delete('/auth/invalidate', [
            'uses' => 'App\Http\Controllers\Auth\AuthController@deleteInvalidate',
            'as' => 'api.auth.invalidate'
        ]);
    });
});
