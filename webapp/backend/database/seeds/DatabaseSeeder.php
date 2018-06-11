<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function jump($date)
    {
        if(date('w', strtotime($date)) == 6 || date('w', strtotime($date)) == 5)
            return date('Y-m-d G:i:s', strtotime('next monday noon', strtotime($date)));
        else
			return date('Y-m-d G:i:s', strtotime($date . ' +1 day'));
    }   

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 0;
        $enviromentID = 0;

        // factory(App\User::class)->create();
        // factory(App\Enviroment::class, 1)->create()->each(function($enviroment) use (&$i, &$enviromentID){
        //     $enviromentID = $enviroment->id;

        //     factory(App\Person::class, 3000)->create()->each( function($person) use ($enviroment, &$i){
        //         factory(App\Student::class, 1)->create([
        //             'school_id' => $enviroment->id,
        //             'person_id' => $person->id,
        //         ]);
                
        //         factory(App\LunchPattern::class,  1)->create([
        //             'person_id' => $person->id
        //         ]);
                
        //         echo "$i - Student. pattern, person created. \n";
        //         $i++;
        //     });
        // });

        factory(App\PaymentType::class,  1)->create([
            'enviroment_id' => ($enviromentID == 0) ? 1 : $enviromentID
        ])->each(function($paymentType){
            $jsDate = 'Tue May 08 2018 12:00:00';
            $phpDate = date('Y-m-d G:i:s', strtotime($jsDate));
            $iStudents = 0;
            $paymentsID = [];
            
            for($j = 0; $j < 5; $j++){
                for($i = 0; $i < 5; $i++){
                    $students = App\Student::skip(600 * $i)
                        ->take(600)
                        ->with('person.lunchPattern')
                        ->get()
                        ->toArray();
        
                    factory(App\RecordFather::class, 1)->create([
                        'created_at' => $phpDate, 
                        'updated_at'=> $phpDate
                    ])->each( function($recordFather) use(&$iStudents, $students, $phpDate, $paymentType){
                        foreach($students as $student){
                            factory(App\RecordChildren::class, 1)->create([
                                'approved'          => $student['person']['lunch_pattern'][strtolower(date('l', strtotime($phpDate)))],
                                'person_id'         => $student['person']['id'],
                                'created_at'        => $phpDate,
                                'updated_at'        => $phpDate,
                                'record_father_id'  => $recordFather->id,
                                'payment_type_id'   => $paymentType->id  
                            ]);
        
                            echo "$iStudents - Lunch History Created \n";
                            $iStudents++;
                        }
                    });
                    $phpDate = $this->jump($phpDate);
                }
            }
        });
    }
}