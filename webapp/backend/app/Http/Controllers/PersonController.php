<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Person;
use App\Student;
use App\LunchPattern;
use App\Enviroment;
use App\Classroom;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class PersonController extends Controller
{

    /**
     * Get a person by it's id.
     *
     * @param  string  $id
     * @param  Request  $request
     * @return Response
    */
    public function getByID($id, Request $request)
    {
        $person = Person::where('id', $id)->with('lunchPattern', 'student', 'student.classroom')->get()->toArray()[0];

        return response()->json($person, 200);
    }

    /**
     * Get N last persons.
     *
     * @param  integer  $enviromentID
     * @param  integer  $quantity
     * @param  Request  $request
     * @return Response
    */
    public function getMany($enviromentID, $quantity, Request $request)
    {
        $persons = Person::orderBy('id', 'desc')
        ->whereHas('student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
        ->with('lunchPattern', 'student', 'student.classroom')
        ->take($quantity)
        ->get()
        ->toArray();

        return response()->json($persons, 200);
    }

    /**
     * Compare each column from each record in the persons table against the search term provided by the user.
     *
     * @param  integer  $enviromentID
     * @param  string  $searchTerm
     * @param  Request  $request
     * @return Response
    */
    public function search($enviromentID, $searchTerm, $deleted = 0, Request $request)
    {
        $searchTerm = urldecode(trim($searchTerm));
        if(Person::orderBy('id', 'desc')->whereHas('student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})->where('name', 'LIKE', $searchTerm .'%')->exists()){
            $persons = Person::orderBy('id', 'desc')
            ->whereHas('student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
            ->where('name', 'LIKE', $searchTerm .'%')
            ->with('lunchPattern', 'student', 'student.classroom')
            ->get()
            ->toArray();

            return response()->json($persons, 200);            
        }else{
            if($deleted == 0){
                $persons = Person::orderBy('id', 'desc')
                ->whereHas('student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
                ->with('lunchPattern', 'student', 'student.classroom')
                ->get()
                ->toArray();
            }else{
                $persons = Person::orderBy('id', 'desc')
                ->whereHas('student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
                ->withTrashed()
                ->with('lunchPattern', 'student', 'student.classroom')
                ->get()
                ->toArray();
            }
    
            $found = [];
    
            foreach($persons as $person){
                foreach($person as $value){
                    if(!is_array($value)){
                        similar_text($value,urldecode($searchTerm),$percent);
                        if($percent>60){
                            $found[] = $person;
                        }
                    }else{
                        foreach($value as $person){
                            similar_text($person,urldecode($searchTerm),$percent);
                            if($percent>80){
                                $found[] = $person;
                            }
                        }
                    }
                }
            }
    
            if(sizeof($found)>0)
                return response()->json($found, 200);
            else
                return response()->json([], 404);
        }

    }

    /**
     * Edit the person
     *
     * @param  string  $id
     * @param  Request  $request
     * @return Response
    */
    public function editPerson($id, Request $request)
    {
        $person = $request->input();

        Student::where('id', $id)->update([
            'registry'  => $person['student']['registry'],
            'isBoarder' => $person['student']['isBoarder'],
        ]);

        Person::where('id', $id)->update([
            'name'      => $person['name'],
            'photo'     => $person['photo'],
        ]);

        if(!isset($person['lunch_pattern']['id'])){
            $person['lunch_pattern']['person_id'] = $id;
            LunchPattern::create($person['lunch_pattern']);
        }else{
            foreach($person['lunch_pattern'] as $weekday => $value){
                LunchPattern::where('id', $person['lunch_pattern']['id'])->update([
                    $weekday => $value
                ]);                
            }
        }
    }

    /**
     * Delete one or multiple students
     *
     * @param  Request  $request
     * @return Response
    */
    public function deletePerson(Request $request)
    {
        $ids = is_array($request->input()) ? $request->input() : array($request->input());
        foreach($ids as $id){
            Person::where('id', $id)->delete();
        }
    }

    /**
     * Import persons
     *
     * @param  Request  $request
     * @return Response
    */
    public function import($enviromentID, Request $request)
    {
        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        $unparseds = $worksheet->toArray();
        $rows = [];
        foreach($unparseds as $unparsed){
            if($unparsed[1] != 'TOTAL DE ALUNOS AUTORIZADOS POR DIA' && $unparsed[1] != 'MÉDIA' && $unparsed[2] != 'TURMA' && !empty($unparsed[2])){
                if(!Classroom::where('name', $unparsed[2])->exists()){
                    $classroom = Classroom::create([
                        'name'      => $unparsed[2],
                        'school_id' => $enviromentID
                    ]);
                }

                $classroomID = Classroom::where('name', $unparsed[2])
                ->get()
                ->pluck('id')
                ->toArray()[0];

                
                
                array_push($rows, [
                    'name'      => trim($unparsed[1]), 
                    'registry'  => intval($unparsed[0]), 
                    'isBoarder' => intval($unparsed[8]),
                    'class_id'  => intval($classroomID),
                    'monday'    => intval($unparsed[3]), 
                    'tuesday'   => intval($unparsed[4]), 
                    'wednesday' => intval($unparsed[5]), 
                    'thursday'  => intval($unparsed[6]), 
                    'friday'    => intval($unparsed[7]), 
                ]);
            }
        }

        unset($unparsed);
        
        foreach($rows as $row){

            $person_id = Person::orderBy('id', 'desc')
            ->where('name', $row['name'])
            ->whereHas('student', function($q) use ($row, $enviromentID){$q->where([['school_id', '=', $enviromentID], ['registry', '=', $row['registry']]]);})
            ->get()
            ->pluck('id')
            ->toArray();

            if(sizeof($person_id)==0){
                $person = new Person;
                $person->name = $row['name'];
                $person->save();
                
                $person->student()->create([
                    'registry'  => $row['registry'],
                    'isBoarder' => $row['isBoarder'],
                    'person_id' => $person->id,
                    'school_id' => $enviromentID,
                    'class_id'  => $row['class_id']
                ]);

                $person->lunchPattern()->create([
                    'person_id' => $person->id,                
                    'monday'    => $row['monday'] , 
                    'tuesday'   => $row['tuesday'], 
                    'wednesday' => $row['wednesday'],
                    'thursday'  => $row['thursday'], 
                    'friday'    => $row['friday'] 
                ]);
            }else{
                $person_id = $person_id[0];
                $person = Person::find($person_id);
                $person->name = $row['name'];
                $person->save();


                if(LunchPattern::where('person_id', '=', $person_id)->exists()){
                    LunchPattern::where('person_id', $person_id)
                    ->update([              
                        'monday'    => $row['monday'] , 
                        'tuesday'   => $row['tuesday'], 
                        'wednesday' => $row['wednesday'],
                        'thursday'  => $row['thursday'], 
                        'friday'    => $row['friday'] 
                    ]);
                }else{
                    $person->lunchPattern()->create([
                        'person_id' => $person->id,                
                        'monday'    => $row['monday'] , 
                        'tuesday'   => $row['tuesday'], 
                        'wednesday' => $row['wednesday'],
                        'thursday'  => $row['thursday'], 
                        'friday'    => $row['friday'] 
                    ]);                        
                }

                Student::where('person_id', $person_id)
                ->update([              
                    'isBoarder' => $row['isBoarder'],
                    'class_id'  => $row['class_id']                 
                ]);
            }
        }
    }
}
