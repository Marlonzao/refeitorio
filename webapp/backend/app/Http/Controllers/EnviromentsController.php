<?php

namespace App\Http\Controllers;

use App\Enviroment;
use App\PaymentType;
use Illuminate\Http\Request;

class EnviromentsController extends Controller
{
    /**
     * Get all enviroments.
     *
     * @param  Request  $request
     * @return Response
    */
    public function getAll(Request $request)
    {
        $enviroments = Enviroment::all()->toArray();

        if(sizeof($enviroments)>0)
            return response()->json($enviroments, 200);
        else
            return response()->json($enviroments, 404);
    }

    /**
     * Register an new enviroment
     *
     * @param  Request  $request
     * @return Response
    */
    public function register(Request $request)
    {
        $enviroment = Enviroment::create(['name'=>$request->input()['name'],'type'=>$request->input()['type']])->toArray();
        
        PaymentType::create([
            'type' => 'byEnviroment',
            'value' => $request->input()['value']['byEnviroment'],
            'enviroment_id' => $enviroment['id']
        ]);

        PaymentType::create([
            'type' => 'byPerson',
            'value' => $request->input()['value']['byPerson'],
            'enviroment_id' => $enviroment['id']
        ]);
    }
}
