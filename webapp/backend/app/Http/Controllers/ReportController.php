<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use raelgc\view\Template; 
use App\RecordChildren;
use App\RecordFather;
use App\Enviroment;
use PDF;

class ReportController extends Controller
{    
    // /**
    //  * Get the total related to the given date
    //  *
    //  * @param  string  $date
    //  * @param  Request  $request
    //  * @return Response
    // */
    // public function getTotalByDate($date, Request $request)
    // {
    //     $reports = History::where('created_at', 'like', $date . '%')->get()->toArray();
    //     // $total = [
    //     //     "date" => $date,
    //     //     "students" => 0,
    //     //     "tickets" => '',
    //     //     "" => '',
    //     // ];
    //     // foreach($reports as $report){

    //     // }
    // }
    
    /**
     * Get totals, limit and offset.
     *
     * @param  int  $offset
     * @param  int  $limit
     * @param  Request  $request
     * @return Response
    */
    public function getAllTotal($enviromentID, $offset = 0, $limit = 10, Request $request)
    {
        $historics = RecordFather::orderBy('id', 'desc')
        ->with('recordChildren', 'recordChildren.paymentType')
        ->whereHas('recordChildren.person.student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
        ->skip((10 * $offset) - 10)
        ->take($limit)
        ->get()
        ->toArray();
        
        $totalHistorics = [];
        foreach($historics as $historic){
            $historicID = $historic['id'];
            $totalEarnings = 0;

            foreach($historic['record_children'] as $recordChildren){
                if($recordChildren['approved'] == 1)
                    $totalEarnings += $recordChildren['payment_type']['value'];
            }

            $totalByEnviroment = RecordChildren::where('approved', 1)
            ->whereHas('recordFather', function($q) use ($historicID){$q->where('id', $historicID);})
            ->count();

            $totalHistorics[] = [
                'id'            => $historic['id'],
                'date'          => date("d/m/Y", strtotime($historic['created_at'])), 
                'byEnviroment'  => $totalByEnviroment, 
                'byPerson'      => null,
                'totalEarnings' => $totalEarnings
            ];
        }

        return response()->json(array('historics'=>$totalHistorics, 'totalCount'=> RecordFather::all()->count()), 200);    
    }

    /**
     * Generate month report.
     *
     * @param  int  $historicID
     * @param  Request  $request
     * @return Response
    */
    public function generateMonthlyReports($enviromentID, $offset = 0, $limit = 10, Request $request)
    {
        $historics = RecordFather::orderBy('id', 'desc')
        ->with('recordChildren', 'recordChildren.paymentType')
        ->whereHas('recordChildren.person.student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
        ->skip((10 * $offset) - 10)
        ->take($limit)
        ->get()
        ->toArray();
        
        $totalHistorics = [];
        foreach($historics as $historic){
            $historicID = $historic['id'];
            $totalEarnings = 0;

            foreach($historic['record_children'] as $recordChildren){
                if($recordChildren['approved'] == 1)
                    $totalEarnings += $recordChildren['payment_type']['value'];
            }

            if(in_array(date("m/Y", strtotime($historic['created_at'])), array_column($totalHistorics, 'month'))){
                $index = array_search(date("m/Y", strtotime($historic['created_at'])), array_column($totalHistorics, 'month'));
                $totalHistorics[$index]['totals']['totalEarnings'] += (float) $totalEarnings;
                $totalHistorics[$index]['totals']['byEnviroment']  += RecordChildren::where('approved', 1)
                ->whereHas('recordFather', function($q) use ($historicID){$q->where('id', $historicID);})
                ->count();
            }else{                
                $totalByEnviroment = RecordChildren::where('approved', 1)
                ->whereHas('recordFather', function($q) use ($historicID){$q->where('id', $historicID);})
                ->count();
    
                $totalHistorics[] = [                
                    'month'     => date("m/Y", strtotime($historic['created_at'])),
                    'totals'    => [
                        'byEnviroment'  => $totalByEnviroment, 
                        'byPerson'      => null,
                        'totalEarnings' => $totalEarnings
                    ]
                ];
            }
        }
        return response()->json(array('historics'=>$totalHistorics, 'totalCount'=> sizeof($totalHistorics)), 200);    
    }
    
    /**
     * Download monthly report
     *
     * @param  int  $enviromentID
     * @param  string  $date
     * @param  Request  $request
     * @return Responsecomposer require barryvdh/laravel-dompdf

    */
    public function downloadMonthlyReport($enviromentID, $date, $type, Request $request)
    {   
        // ini_set('memory_limit','900M');

        $total_enviroment_persons = 0;
        $enviroment_total = 0;
        $total_paid_persons = 0;
        $paid_persons_total = 0;

        
        $enviroment = Enviroment::where('id', $enviromentID)
        ->with('paymentType')
        ->get()
        ->toArray()[0];
        
        $reports = RecordFather::orderBy('id', 'desc')
        ->where('created_at', 'like', '%' . \DateTime::createFromFormat("m-Y", $date)->format('Y-m') . '%')
        ->whereHas('recordChildren.person.student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
        ->with('recordChildren', 'recordChildren.paymentType', 'recordChildren.person', 'recordChildren.person.student', 'recordChildren.person.student.school')
        ->get()
        ->toArray();

        if($type == 'PDF'){
            $pdf = new \Nextek\LaraPdfMerger\PdfManage;
    
            $i = 0;
            $tplHead = new Template(__DIR__.'/templates/monthlyReport/monthlyReportHead.html');
            $tplHead->MONTH = str_replace("-","/",$date);
            
            PDF::loadHTML($tplHead->parse())->save(__DIR__.'/templates/monthlyReport/dist/monthlyReportHead.pdf');
            $pdf->addPDF(__DIR__.'/templates/monthlyReport/dist/monthlyReportHead.pdf', 'all');
    
            unset($tplHead);
    
            foreach($reports as $report){
                $tplDays = new Template(__DIR__.'/templates/monthlyReport/dayPart.html');
                $tplDays->DAY = date("d/m/Y", strtotime($report['created_at']));
                $tplDays->ENVIROMENT_REGISTRY_TYPE = ($enviroment['type']=='school') ? 'Matrícula' : 'Registro';
    
                foreach($report['record_children'] as $reportChildren){
                    if($reportChildren['approved']){
                        $tplDays->MOMENT = date("G:i:s d/m/Y", strtotime(($reportChildren['created_at'])));
                        $tplDays->REGISTRY = $reportChildren['person']['student']['registry'];
                        $tplDays->PERSON_NAME = $reportChildren['person']['name'];
                        $tplDays->PAYMENT_VALUE = $reportChildren['payment_type']['value'];
                        $tplDays->block('BLOCK_CHILD_REPORT');
                    }
                    
                    if($reportChildren['payment_type']['type'] == 'byEnviroment' && $reportChildren['approved']){
                        $total_enviroment_persons++;
                        $enviroment_total += $reportChildren['payment_type']['value'];
                    }elseif($reportChildren['payment_type']['type'] == 'byPerson' && $reportChildren['approved']){
                        $total_paid_persons++;
                        $paid_persons_total += $reportChildren['payment_type']['value']; 
                    }
                }
                PDF::loadHTML($tplDays->parse())->save(__DIR__."/templates/monthlyReport/dist/monthlyReportDays$i.pdf");
                $pdf->addPDF(__DIR__."/templates/monthlyReport/dist/monthlyReportDays$i.pdf", 'all');
                $i++;   
            }
        
            $tplFooter = new Template(__DIR__.'/templates/monthlyReport/monthlyReportFooter.html');
    
            if(sizeof($enviroment['payment_type']) == 2){
                $tplFooter->ENVIROMENT_VALUE = $enviroment['payment_type'][array_search('byEnviroment', array_column($enviroment['payment_type'], 'type'))]['value'];
                $tplFooter->PAID_PERSON_VALUE = $enviroment['payment_type'][array_search('byPerson', array_column($enviroment['payment_type'], 'type'))]['value'];
            }
    
            $tplFooter->PERSON_TYPE = ($enviroment['type']=='school') ? 'Aluno' : 'Funcionário';
            $tplFooter->ENVIROMENT_TYPE = ($enviroment['type']=='school') ? 'Escola' : 'Empresa';
            $tplFooter->ENVIROMENT_TOTAL = $enviroment_total;
            $tplFooter->TOTAL_ENVIROMENT_PERSONS = $total_enviroment_persons;
    
            $tplFooter->TOTAL_PAID_PERSONS = $total_paid_persons;
            $tplFooter->PAID_PERSON_TOTAL = $paid_persons_total;
    
            $tplFooter->TOTAL_PERSONS = $paid_persons_total + $total_enviroment_persons;
            $tplFooter->TOTAL = $paid_persons_total + $enviroment_total;
    
            PDF::loadHTML($tplFooter->parse())->save(__DIR__.'/templates/monthlyReport/dist/monthlyReportFooter.pdf');
            $pdf->addPDF(__DIR__.'/templates/monthlyReport/dist/monthlyReportFooter.pdf', 'all');
    
            $result = $pdf->merge('string');
    
            foreach(glob(__DIR__.'/templates/monthlyReport/dist/*') as $file){
                if(is_file($file))
                    unlink($file);
            }
    
            return $result;
        }elseif($type == 'XLSX'){
            $reports = array_reverse($reports);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getColumnDimension('A')->setWidth(35);
            $sheet->getColumnDimension('B')->setWidth(25);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->setCellValue('A1', 'Entradas do mês de - '.str_replace("-","/",$date));
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffcc00');
            $sheet->mergeCells('A1:B1');

            $i = 1;
            $j = 1;
            foreach($reports as $report){
                $j = 1;
                $header = (9+$i);

                $sheet->setCellValue('A'.(8+$i), 'Entradas do dia: '.date("d/m/Y", strtotime($report['created_at'])));                
                $sheet->getStyle('A'.(8+$i).':'.'D'.(8+$i))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffcc00');
                $sheet->getStyle('A'.(8+$i))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);                
                $sheet->mergeCells('A'.(8+$i).':'.'D'.(8+$i));
                
                $sheet->setCellValue("A$header", 'Momento');
                $sheet->getStyle("A$header")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("B$header", 'Matrícula');
                $sheet->getStyle("B$header")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("C$header", 'Nome');
                $sheet->getStyle("C$header")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                // $sheet->setCellValue("D$header", 'Tipo');
                // $sheet->getStyle("D$header")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue("D$header", 'Preço');
                $sheet->getStyle("D$header")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $spreadsheet->getActiveSheet()->getStyle("A$header:D$header")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('373737');
                $spreadsheet->getActiveSheet()->getStyle("A$header:D$header")->getFont()->getColor()->setARGB('f4f4f4');

                $firstCellPosition = null;
                foreach($report['record_children'] as $reportChildren){
                    if($reportChildren['approved']){
                        // if($reportChildren['payment_type']['type'] == 'byEnviroment'){
                        //     $type = 'Subsidiado';
                        // }elseif($reportChildren['payment_type']['type'] == 'byPerson'){
                        //     $type = 'Aluno Pago';
                        // }

                        $cellPosition = ($header+$j);
                        $firstCellPosition = ($firstCellPosition == null) ? $cellPosition : $firstCellPosition; 

                        $cell = 'A'.$cellPosition;
                        $sheet->setCellValue($cell, date("G:i:s d/m/Y", strtotime($reportChildren['created_at'])));
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f4f4f4');

                        $cell = 'B'.$cellPosition;
                        $sheet->setCellValue($cell, $reportChildren['person']['student']['registry']);
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f4f4f4');

                        $cell = 'C'.$cellPosition;
                        $sheet->setCellValue($cell, $reportChildren['person']['name']);
                        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f4f4f4');

                        // $cell = 'D'.$cellPosition;
                        // $sheet->setCellValue($cell, $type);
                        // $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        // $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f4f4f4');

                        $cell = 'D'.$cellPosition;
                        $sheet->setCellValue($cell, $reportChildren['payment_type']['value']);
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode("\"R$\"General");

                        $lastCellPosition = $cellPosition;
                        $j++;
                    }
                }
                $dayTableRange = "A$firstCellPosition:D$lastCellPosition";
                $sheet->getStyle($dayTableRange)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]]);

                // $cellPosition = $header+$j;
                // $sheet->setCellValue("A$cellPosition", 'TOTAL ALUNOS SUBSIDIADOS');
                // $sheet->setCellValue("E$cellPosition", "=SUMIF(D$firstCellPosition:D$lastCellPosition, \"Subsidiado\", E$firstCellPosition:E$lastCellPosition)");
                // $sheet->getStyle("A$cellPosition:D$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff99');
                // $sheet->mergeCells("A$cellPosition:D$cellPosition");                
                // $sheet->getStyle("E$cellPosition")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                // $sheet->getStyle("E$cellPosition")->getNumberFormat()->setFormatCode("\"R$\"General");
                // $sheet->getStyle("E$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');
                // $j++;

                // $cellPosition = $header+$j;                
                // $sheet->setCellValue("A$cellPosition", 'TOTAL ALUNOS PAGOS');
                // $sheet->setCellValue("E$cellPosition", "=SUMIF(D$firstCellPosition:D$lastCellPosition, \"Aluno Pago\", E$firstCellPosition:E$lastCellPosition)");
                // $sheet->getStyle("A$cellPosition:D$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff99');
                // $sheet->mergeCells("A$cellPosition:D$cellPosition");                
                // $sheet->getStyle("E$cellPosition")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                // $sheet->getStyle("E$cellPosition")->getNumberFormat()->setFormatCode("\"R$\"General");
                // $sheet->getStyle("E$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');                
                // $j++;

                $cellPosition = $header+$j;                
                $sheet->setCellValue("A$cellPosition", 'TOTAL DIA');
                $sheet->setCellValue("D$cellPosition", "=SUM(D$firstCellPosition:D$lastCellPosition)");
                $sheet->getStyle("A$cellPosition:C$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff99');
                $sheet->mergeCells("A$cellPosition:C$cellPosition");                
                $sheet->getStyle("D$cellPosition")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D$cellPosition")->getNumberFormat()->setFormatCode("\"R$\"General");
                $sheet->getStyle("D$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');                
                $j++;

                $cellPosition = $header+$j;                
                $sheet->setCellValue("A$cellPosition", 'NÚMERO DE ALUNOS');
                $sheet->getStyle("A$cellPosition:C$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff99');
                $sheet->mergeCells("A$cellPosition:C$cellPosition");                
                $sheet->setCellValue("D$cellPosition", "=ROWS($dayTableRange)");
                $sheet->getStyle("D$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');                
                $j++;
                
                // $cellPosition = $header+$j;                
                // $sheet->setCellValue("A$cellPosition", 'NÚMERO DE ALUNOS SUBSIDIADOS');
                // $sheet->getStyle("A$cellPosition:D$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff99');
                // $sheet->mergeCells("A$cellPosition:D$cellPosition");                
                // $sheet->setCellValue("E$cellPosition", "=COUNTIF(D$firstCellPosition:D$lastCellPosition,\"Subsidiado\")");
                // $sheet->getStyle("E$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');                
                // $j++;
                
                // $cellPosition = $header+$j;                
                // $sheet->setCellValue("A$cellPosition", 'NÚMERO DE ALUNOS PAGOS');
                // $sheet->getStyle("A$cellPosition:D$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffff99');
                // $sheet->mergeCells("A$cellPosition:D$cellPosition");                
                // $sheet->setCellValue("E$cellPosition", "=COUNTIF(D$firstCellPosition:D$lastCellPosition,\"Aluno Pago\")");
                // $sheet->getStyle("E$cellPosition")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('dcd0c0');                
                // $j++;

                $i = (($i+2)+($j+1));
            }
            
            $sheet->setCellValue('A2', 'TOTAL MÊS');
            $sheet->setCellValue('B2', "=SUMIF(A:A, \"TOTAL DIA\", D:D)");
            $sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("B2")->getNumberFormat()->setFormatCode("\"R$\"General");

            // $sheet->setCellValue('A3', 'TOTAL MÊS ALUNOS SUBSIDIADOS');
            // $sheet->setCellValue('B3', "=SUMIF(A:A, \"TOTAL ALUNOS SUBSIDIADOS\", E:E)");
            // $sheet->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            // $sheet->getStyle("B3")->getNumberFormat()->setFormatCode("\"R$\"General");

            // $sheet->setCellValue('A4', 'TOTAL MÊS ALUNOS PAGOS');
            // $sheet->setCellValue('B4', "=SUMIF(A:A, \"TOTAL ALUNOS PAGOS\", E:E)");
            // $sheet->getStyle('B4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            // $sheet->getStyle("B4")->getNumberFormat()->setFormatCode("\"R$\"General");

            $sheet->setCellValue('A3', 'NÚMERO DE ALUNOS NO MÊS');
            $sheet->setCellValue('B3', "=SUMIF(A:A, \"NÚMERO DE ALUNOS\", D:D)");

            // $sheet->setCellValue('A6', 'NÚMERO DE ALUNOS SUBSIDIADOS NO MÊS');
            // $sheet->setCellValue('B6', "=SUMIF(A:A, \"NÚMERO DE ALUNOS SUBSIDIADOS\", E:E)");

            // $sheet->setCellValue('A7', 'NÚMERO DE ALUNOS PAGOS NO MÊS');
            // $sheet->setCellValue('B7', "=SUMIF(A:A, \"NÚMERO DE ALUNOS PAGOS\", E:E)");

            $writer = new Xlsx($spreadsheet);
            $writer->save(__DIR__.'/templates/dist.xlsx');
            $return = file_get_contents(__DIR__.'/templates/dist.xlsx');

            unlink(__DIR__.'/templates/dist.xlsx');

            return $return;
        }
    }
    
    /**
     * Generate a report to a given ID.
     *
     * @param  int  $historicID
     * @param  Request  $request
     * @return Response
    */
    public function generateReport($historicID, $offset = 0, $limit = 10, Request $request)
    {
        $reports = RecordChildren::orderBy('id', 'desc')
        ->whereHas('recordFather', function($q) use($historicID){$q->where('id', $historicID);})
        ->where('approved', 1)
        ->with('paymentType', 'person', 'person.student')
        ->skip((10 * $offset) - 10)
        ->take($limit)
        ->get()
        ->toArray();
        
        // foreach($reports as &$report){
        //     $report['created_at'] = date("d/m/Y", strtotime($report['created_at']));
        // }

        return response()->json(array(
            'reports' => $reports, 
            'totalCount'=> RecordChildren::whereHas('recordFather', function($q) use($historicID){$q->where('id', $historicID);})->count()), 200); 
    }

    /**
     * Gera medias por dias de semana.
     *
     * @param  int  $enviromentID
     * @param  Request  $request
     * @return Response
    */
    public function generateChartWeekReport($enviromentID, Request $request)
    {
        $records_father = RecordFather::orderBy('id', 'desc')
        ->with('recordChildren', 'recordChildren.paymentType')
        ->whereHas('recordChildren.person.student.school', function($q) use ($enviromentID){$q->where('id', $enviromentID);})
        ->whereHas('recordChildren', function($q) {$q->where('approved', 1);})
        ->get()
        ->toArray();

        $i = array_fill(0, 7, 1);
        $template = array_fill(0, 7, 0);

        $totalsPersons  = $template;
        $totalsIncome   = $template;

        foreach($records_father as $record_father){
            $totalEarnings  = 0;
            $weekday        = date('w', strtotime($record_father['created_at']));

            foreach($record_father['record_children'] as $recordChildren){
                $totalEarnings += $recordChildren['payment_type']['value'];
            }

            $totalsIncome[$weekday]  += $totalEarnings; 
            $totalsIncome[$weekday]  /= $i[$weekday];
            
            $totalsPersons[$weekday] += sizeof($record_father['record_children']);
            $totalsPersons[$weekday] /= $i[$weekday];

            $totalsPersons[$weekday] = round($totalsPersons[$weekday]);
            $totalsIncome[$weekday]  = round($totalsIncome[$weekday], 2);
            
            $i[$weekday]++;
        }

        return response()->json([$totalsIncome, $totalsPersons], 200); 
    }

    public function returnExcelSum($cells){
        $total = '=';
        foreach($cells as $cell){
            $total .= ($total == '=') ? $cell : "+$cell";
        }
        return $total;
    }

    public function changeRange($range, $letter){
        $noLetters = preg_replace("/[^0-9,.]/", "", $range);

    }
}