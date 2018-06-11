<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RecordChildren;
use App\RecordFather;
use App\Person;
use App\LunchPattern;
use App\Enviroment;
use App\PaymentType;
use DB;

class HistoryController extends Controller
{
    /**
     * Check if the person was not added in a report today.
     *
     * @param  int  $personID
     * @return Response
     **/
    public function checkHistoryMirror($personID)
    {
        return response()->json($this->checkHistory($personID), 200);
    }

    /**
     * Check if the person was not added in a report today.
     *
     * @param  int  $personID
     * @return boolean
    */
    public function checkHistory($personID)
    {
        $hours = RecordChildren::orderBy('id', 'desc')
        ->where('person_id',$personID)
        ->whereHas('recordFather', function($q) use ($personID){$q->where('created_at', 'like', '%' . date('Y-m-d') . '%');})
        ->get()
        ->pluck('created_at')
        ->toArray();
        
        $noon = false;
        $late = false;
        foreach($hours as $hour){
            $time = $hour->subDay()->format('H');
            if($time >= 11 && $time < 14)
                $noon = true;
            if($time >= 18 && $time < 21)
                $late = true;
        }

        return ['noon'=>$noon, 'late'=>$late];
    }

    /**
     * Check if the person was not added in a report today.
     *
     * @param  array  $conflict
     * @param  boolean $isBoarder
     * @return boolean
    */
    public function allow($conflict, $isBoarder)
    {
        $time = date('H');        
        if($isBoarder){
            if(($time >= 11 && $time < 14) && $conflict['noon']){
                return false;
            }elseif(($time >= 18 && $time < 21) && $conflict['late']){
                return false;
            }
        }elseif(($time >= 11 && $time < 14) && $conflict['noon']){
            return false;
        }

        return true;
    }

    /**
     * Register the meal of a given person by it's id.
     *
     * @param  int  $personID
     * @param  string  $paymentType
     * @param  Request  $request
     * @return Response
    */
    public function register($personID, $paymentType, $enviromentID)
    {
        $conflict = $this->checkHistory($personID);
        $isBoarder = Person::where('id', $personID)
        ->with('student')
        ->get()
        ->toArray()[0]['student']['isBoarder'];

        if(RecordFather::orderBy('id', 'desc')
        ->where('created_at', 'like', '%' . date('Y-m-d') . '%')
        ->exists()){

            if(!$this->allow($conflict, $isBoarder)){
                return response()->json([], 409);                
            }

            $record_father = RecordFather::orderBy('id', 'desc')
            ->where('created_at', 'like', '%' . date('Y-m-d') . '%')
            ->get()
            ->toArray()[0];   
        }else{
            $record_father = RecordFather::create()->toArray();
        }

        $paymentID = PaymentType::where('type', $paymentType)
        ->whereHas('enviroments', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
        ->get()
        ->pluck('id')[0];

        if($paymentType == 'byEnviroment'){
            $time = date('H');
            if($time >= 11 && $time < 14){
                $approved = (LunchPattern::orderBy('id', 'desc')
                ->whereHas('person', function($q) use ($personID){$q->where('id', $personID);})
                ->get()
                ->toArray()[0][strtolower(date('l'))] 
                    ||
                $isBoarder);
            }elseif($time >= 18 && $time < 21){
                $approved = $isBoarder;
            }else{
                $approved = 0;
            }

            RecordChildren::create([
                'approved'          => $approved,
                'person_id'         => $personID,
                'record_father_id'  => $record_father['id'],
                'payment_type_id'   => $paymentID
            ]);

            if($approved)
                return response()->json([], 200);            
            else
                return response()->json([], 403);            
            
        }elseif($paymentType == 'byPerson'){
            RecordChildren::create([
                'approved'          => 1,
                'person_id'         => $personID,
                'record_father_id'  => $record_father['id'],
                'payment_type_id'   => $paymentID
            ]);

            return 1;            
        }
    }
}
