<?php

namespace App\Exports;

use App\AttendDetail;
use App\Api_Employees;
use Maatwebsite\Excel\Sheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Barryvdh\Debugbar\Facade as Debugbar;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class AttendDetailsExport implements FromCollection,WithEvents, WithCustomStartCell ,WithHeadings,WithTitle

{
    use Exportable;
    public $printY = 0;
    public $attendTime = null;
    public $workdays = array();
    public $namedays = array();

    public $type = null;
    public $month = null;
    public $year =null;
    public $day_count = null; 

    public  $empArray = array();
    public $csvArray = array();
    
    public function __construct(int $printY) 
    {
        $this->printY = $printY;
        $this->type = CAL_GREGORIAN;
        $this->month = date(substr((string)$this->printY,4)); // Month ID, 1 through to 12.
        $this->year = date(substr((string)$this->printY,0,4)); // Year in 4 digit 2009 format.
        $this->day_count = cal_days_in_month($this->type, $this->month, $this->year); // G
    }

    public function collection()
    {
        //that is temporary code for nishimura's testing  
        //it will be use when stay at home is over.
        $content=file_get_contents(env("MIX_APP_AungThiHa_URL")."/employees");
        $empApiArray = json_decode($content, true);
       
        // APIから取得する社員情報
        for ($i = 0; $i < count($empApiArray); $i++) 
        {
            $empSubArray = array();
            array_push($empSubArray, $i);
            $empSubArray['employeeId'] = $empApiArray[$i]['id'];
            $empSubArray['employeeNo'] = $empApiArray[$i]['employeeId'];
            $empSubArray['name'] = $empApiArray[$i]['name'];
            array_push($this->empArray,  $empSubArray);
        }

        // attend_detailsテーブルから取得した通勤情報
        $this->attendTime = DB::select('select date,emp_no,
            CASE 
                WHEN am_leave = 1 || pm_leave = 1 THEN "P" 
                WHEN am_leave = 2 && pm_leave = 2 THEN "X"
                ELSE ROUND((total_hours * 1.00)/8,2)
            END as total_hours,am_leave, pm_leave
        from attend_details 
        where EXTRACT(YEAR_MONTH FROM date) = :date order by emp_no,date asc', ['date' => $this->printY]);
        $this->attendTime = json_decode(json_encode($this->attendTime),true);
       

        // attend_detailsテーブルから取得したデータを配列する
        $totalArray1 = array();
        for($i = 0; $i < count($this->attendTime) ; $i++)
        {
            $eachArray1 = array();
            $eachArray1["emp_no"] =$this->attendTime[$i]["emp_no"];
            $eachArray1[$this->attendTime[$i]["date"]] =$this->attendTime[$i]["total_hours"];
            $eachArray1["am_leave"] =$this->attendTime[$i]["am_leave"];
            $eachArray1["pm_leave"] =$this->attendTime[$i]["pm_leave"];
            array_push($totalArray1,$eachArray1);
        }
       
        // employeesテーブルから取得した社員明細情報
        $empDetailArray = array();
        $empDetailArray = DB::select('select kana_name,entry_date,emp_id,
        case
            when TIMESTAMPDIFF(year,DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) = 1 then 6
            when TIMESTAMPDIFF(year,DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) > 1 then 16
            else 0
        end as holiday from employees order by emp_id');

        // employeesテーブルから取得したデータを月によって日付配列を作成する
       $totalArray2 = array();
        foreach($empDetailArray as $emp)
        {
            $eachArray2 = array();
            $eachArray2["emp_no"] = $emp->emp_id;
            
            for ($i = 1; $i <= $this->day_count; $i++)
            {
                $date=date_create($this->year.'-'.$this->month.'-'.$i);
                $eachArray2[$date->format('Y-m-d')] = 0;
            }
           
            for ($j = 0; $j < count($this->empArray); $j++) 
            {
               if ($this->empArray[$j]['employeeId'] == $emp->emp_id) {
                    $eachArray2["empNo"] = $this->empArray[$j]['employeeNo'];
                    $eachArray2["kana_name"] = $this->empArray[$j]['name']."\n"."(".$emp->kana_name.")"; break; 
                } 
               
            }
            $eachArray2["holiday"] = $emp->holiday;
            array_push($totalArray2,$eachArray2);
        }
        
        $j = 0; 
        for($z = 0; $z < count($totalArray2); $z++)
        {
            for ($i = 1; $i <= $this->day_count; $i++)
            {
                $date =date_create($this->year.'-'.$this->month.'-'.$i);
                $date =$date->format('Y-m-d');
                
                if($j < count($totalArray1))
                {
                    if($totalArray2[$z]["emp_no"] == $totalArray1[$j]["emp_no"] && array_key_exists($date,$totalArray1[$j]))
                    {
                        $totalArray2[$z][$date] =  $totalArray1[$j][$date];
                        $j++;
                    }
                }
            }
         }

        // 月によって各社員の時間合計
         $attendSumTime = DB::select('select emp_no, SUM(total_hours) as total_hours 
                from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date 
                group by emp_no order by emp_no', 
                ['date' => $this->printY]);
        
        $attendSumTime = json_decode(json_encode( $attendSumTime),true);
        
       // まとめ
        $j = 0; $paidHoliday = 0;  $unpaidHoliday = 0; $leaveArray = array();
         for ($i = 0; $i < count($totalArray1); $i++) 
         {
            if($totalArray1[$i]['emp_no'] == $totalArray2[$j]["emp_no"] && 
              ($totalArray1[$i]['am_leave'] == 1 || $totalArray1[$i]['pm_leave'] == 1))
            {
                $paidHoliday += 0.5;
            }else if($totalArray1[$i]['emp_no'] == $totalArray2[$j]["emp_no"] &&
                ($totalArray1[$i]['am_leave'] == 2 || $totalArray1[$i]['pm_leave'] == 2))
            {
                $unpaidHoliday += 1;
            }else if($totalArray1[$i]['emp_no'] != $totalArray2[$j]["emp_no"])
            {
                $leaveSubArray = array();
                array_push($leaveSubArray,$paidHoliday);
                array_push($leaveSubArray,$unpaidHoliday);
                array_push($leaveArray,$leaveSubArray);
                $paidHoliday = 0;  $unpaidHoliday = 0; $j++;
                if($totalArray1[$i]['emp_no'] == $totalArray2[$j]["emp_no"] &&
                    ($totalArray1[$i]['am_leave'] == 1 || $totalArray1[$i]['pm_leave'] == 1))
                {
                    $paidHoliday += 0.5;
                }else if($totalArray1[$i]['emp_no'] == $totalArray2[$j]["emp_no"] &&
                    ($totalArray1[$i]['am_leave'] == 2 || $totalArray1[$i]['pm_leave'] == 2))
                {
                    $unpaidHoliday += 1;
                }
            }
            //$j++;
        }
        $leaveSubArray = array();
        array_push($leaveSubArray,$paidHoliday);
        array_push($leaveSubArray,$unpaidHoliday);
        array_push($leaveArray,$leaveSubArray);

         //有休計算
         $yuukyuu = DB::select('select entry_date,emp_id, DATE_ADD(entry_date, INTERVAL 3 MONTH) as paid_start_date, 
         TIMESTAMPDIFF(year, DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) as working_year, 
         case when TIMESTAMPDIFF(year, DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) >=1 then 16 
              when TIMESTAMPDIFF(year, DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) = 0 then 6 
              else 0 end as holiday,
              case when TIMESTAMPDIFF(year, DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) >=1 then 
              DATE_ADD(DATE_ADD(entry_date, INTERVAL 3 MONTH),INTERVAL TIMESTAMPDIFF(year, DATE_ADD(entry_date, INTERVAL 3 MONTH), now()) YEAR) 
              when TIMESTAMPDIFF(year, DATE_ADD(entry_date, INTERVAL 3 MONTH), now() ) = 0 then 0 
              else 0 end as paid_start_year
             from employees
             order by emp_id');
         
         $yuukyuu = json_decode(json_encode($yuukyuu),true);
 
         $yuukyuuArray = array();
         for($i = 0; $i < count($yuukyuu) ; $i++)
         {
             $yuukyuuSubArray = array();
             $yuukyuuSubArray["emp_no"] =$yuukyuu[$i]["emp_id"];
             $yuukyuuSubArray["holiday"] =$yuukyuu[$i]["holiday"];
             $yuukyuuSubArray["paid_start_date"] =$yuukyuu[$i]["paid_start_date"];
             $yuukyuuSubArray["paid_start_year"] =$yuukyuu[$i]["paid_start_year"];
             array_push($yuukyuuArray,$yuukyuuSubArray);
         }
 
         
         $monthArray = array();
         for($i = 0; $i < count($yuukyuuArray) ; $i++)
         {
             
             $start_date = null;
             
             if($yuukyuuArray[$i]['paid_start_year'] == 0)
             {
                 $start_date = $yuukyuuArray[$i]['paid_start_date'];
             }
             else
             {
                 $start_date = $yuukyuuArray[$i]['paid_start_year'];
             }
             
             $month_difference = date_diff(date_create((string)$this->printY.'01'), date_create($start_date))->format('%m');
             $start_date = (int)str_replace('-','',substr($start_date, 0, strrpos($start_date, '-')));
           
             $monthSubArray = array(); 
             $monthSubArray['emp_no'] =  $yuukyuuArray[$i]['emp_no'];
             $sumHolidayForEach = 0;
            for($j = 0; $j <= $month_difference; $j++)
             {
                 
                $holidayByMonth = DB::select('SELECT SUM(am_leave)+SUM(pm_leave)as yuukyuu, emp_no, EXTRACT(YEAR_MONTH FROM date) as month 
                                     FROM attend_details 
                                     where EXTRACT(YEAR_MONTH FROM date) = :date 
                                     AND ((am_leave = 1 AND pm_leave = 0) OR (am_leave = 1 AND pm_leave = 1) OR (am_leave = 0 AND pm_leave = 1)) AND emp_no = :emp_no
                                     group by month,emp_no', 
                                     ['date' => $start_date,'emp_no'=>$yuukyuuArray[$i]['emp_no']]);
                 $z= 0;
                 
                 $holidayByMonth = json_decode(json_encode($holidayByMonth),true);
                 
          
                
                 if(count($holidayByMonth) == 0)
                 {
                    
                     $sumHolidayForEach += 0;
                     
                 }
                 else
                 {
                   $sumHolidayForEach += $holidayByMonth[$z]["yuukyuu"];
                 }
                 $start_date += 1; 
                 
                }
                $sumHolidayForEach = $yuukyuuArray[$i]['holiday'] - $sumHolidayForEach;
                array_push($monthSubArray,$sumHolidayForEach);
               array_push($monthArray,$monthSubArray);
         }
         
       
        // CSV出力ため配列作成する 
        for ($i = 0; $i < count($attendSumTime); $i++) 
        {
            $empSubArray = array();
            array_push($empSubArray, $i+1);
            foreach($totalArray2 as $array2)
            {
                //emp_no
                //print_r( $array2);
                 if($attendSumTime[$i]['emp_no'] == $array2['emp_no'])
                 {
                    array_push($empSubArray, $array2['empNo']);
                    array_push($empSubArray,  $array2['kana_name']);
                    for ($j = 1; $j <= $this->day_count; $j++)
                    {
                        $date =date_create($this->year.'-'.$this->month.'-'.$j);
                        $date =$date->format('Y-m-d');
                        array_push($empSubArray, $array2[$date]);
                    }
                 }

            }

            array_push($empSubArray,  $attendSumTime[$i]['total_hours']);
            if($leaveArray[$i][0] == 0)
            {
                array_push($empSubArray,  '-');
            }
            else
            {
                array_push($empSubArray,  $leaveArray[$i][0]);
            }
            if($leaveArray[$i][1] == 0)
            {
                array_push($empSubArray,  '-');
            }
            else
            {
                array_push($empSubArray,  $leaveArray[$i][1]);
            }
            array_push($empSubArray,  $monthArray[$i][0]);
            array_push($this->csvArray, $empSubArray);
        }
       return collect($this->csvArray);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class    => function(BeforeExport $event) { 

                // 一番目見出し
                array_push($this->workdays, " ");
                array_push($this->workdays, "");
                array_push($this->workdays, "名前");
               
                // 二番目見出し
                for ($i = 1; $i <= 3; $i++) 
                {
                    array_push($this->namedays, '');
                }
                for ($i = 1; $i <= $this->day_count; $i++) 
                {
                    $this->workdays[] = $i;
                    $d_var = getdate(mktime(0,0,0,$this->month,$i,$this->year));
                    
                    switch($d_var['weekday'])
                    {
                        case "Monday" :
                            array_push($this->namedays, '月');break;
                        case "Tuesday" :
                            array_push($this->namedays, '火');break;
                        case "Wednesday" :
                            array_push($this->namedays, '水');break;
                        case "Thursday" :
                            array_push($this->namedays, '木');break;
                        case "Friday" :
                            array_push($this->namedays, '金');break;
                        case "Saturday" :
                            array_push($this->namedays, '土');break;
                        case "Sunday" :
                            array_push($this->namedays, '日');break;
                    }
                }
                array_push($this->workdays, "まとめ");
                array_push($this->namedays, "出勤数日");
                array_push($this->namedays, "有給休暇");
                array_push($this->namedays, "欠勤数日");
            },
            AfterSheet::class    => function(AfterSheet $event) {

                Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
                    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
                });            
                $event->sheet->styleCells(
                    'D2',
                    [
                        'font' => [
                            'name' =>  'ＭＳ Ｐゴシック',
                            'size' =>  11,
                            'bold' =>  true  
                        ],                      
                    ]
                ); 
                $event->sheet->styleCells(
                    'A6:AK7',
                    [
                        'font' => [
                            'name' =>  'ＭＳ Ｐゴシック',
                            'size' =>  9
                        ],                      
                    ]
                ); 
         
                
                $this->csvArray = json_decode(json_encode($this->csvArray),true);
                
                // 勤怠管理表
                $event->sheet->getDelegate()->mergeCells('D2:AB2');
                $event->sheet->getDelegate()
                    ->getStyle('D2')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                $fontArray = [
                    'font' => [
                            'bold' => true,
                    ]
                ];
                    
                $event->sheet->getStyle('D2')->applyFromArray($fontArray);
                
                $event->sheet->getDelegate()->setCellValue('D2', substr_replace((string)$this->printY, '/', 4, 0) .' 勤怠管理表');


                // （土、日）曜日場合色付け
                $z = 0; $y =0;  $printCell = 0;
                for ($i = 0; $i < count($this->namedays); $i++) 
                {
                    $j = 7; 
                    $cell = 321 + $i; //D
                    if( $cell > 346)
                    {
                        $cell2 = chr(321) ;
                        $cell3 = chr(321+ $z) ;
                        $printCell = $cell2.$cell3;
                        $z++;
                    }else
                    {
                        $cell1 = chr($cell);
                        $printCell = $cell1;
                    }
                        
                    if( $this->namedays[$i] == '土' || $this->namedays[$i] == '日')
                    {
                        $lineStart = 7;
                        $colorRange = $printCell.$lineStart .':'.$printCell.($lineStart + count($this->csvArray)); // All headers
                        $event->sheet->getDelegate()->getStyle($colorRange)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                    }
                }
                
                // border line
                $styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                    
              
                $event->sheet->getStyle('A6:'.$printCell.(count($this->csvArray) + 7))->applyFromArray($styleArray);
                    
                $event->sheet->getDelegate()->getStyle('A6:'.$printCell.'6')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('9DC3E6');
                $event->sheet->getDelegate()->getStyle('A7:'.$printCell.'7')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('9DC3E6');
                    
                $event->sheet->getDelegate()->getStyle('D8:'.$printCell.(count($this->csvArray) + 7))->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

                $event->sheet->getColumnDimension('A')->setWidth(3.2);
                $event->sheet->getColumnDimension('B')->setWidth(7);
                $event->sheet->getColumnDimension('C')->setWidth(27);           
                
                if(count($this->namedays)==37){
                    //  d-ah
                    foreach(range('D','Z') as $v){
                        $event->sheet->getColumnDimension($v)->setWidth(5.8);
                    }
                    foreach(['AA','AB','AC','AD','AE','AF','AG','AH'] as $v ){
                        $event->sheet->getColumnDimension($v)->setWidth(5.8);
                    }                 

                }
                // $event->sheet->getColumnDimension('D8:F8')->setWidth(20);

                    
                $font_color_start = 8;
                for ($i = 0; $i < count($this->csvArray); $i++)
                {
                    $cellA = 320; 
                    for($j = 0; $j < count($this->csvArray[$i]); $j++)
                    {
                        $cell = 321 + $j;
                        if($cell > 346)
                        {
                            $printCell = chr(321).chr(++$cellA);
                        }
                        else
                        {
                            $printCell = chr($cell);
                        }
                            
                        if( $this->csvArray[$i][$j] == 'P' || $this->csvArray[$i][$j] == 'X')
                        {
                            $event->sheet->getDelegate()->getStyle($printCell.$font_color_start)->getFont()->
                                getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                        }
                    
                        if($printCell == chr(321) || $printCell == chr(322) )
                        {}
                        else
                        {
                            if(count($this->namedays) == 37)
                            {
                                if($printCell === "AI" || $printCell === "AJ" || $printCell === "AK")
                                {
                                    $event->sheet->getColumnDimension('AI')->setWidth(8);
                                    $event->sheet->getColumnDimension('AJ')->setWidth(8);
                                    $event->sheet->getColumnDimension('AK')->setWidth(8);
                                    $event->sheet->getDelegate()->getStyle($printCell.$font_color_start)
                                        ->getNumberFormat()
                                        ->setFormatCode('0.0');
                                    $event->sheet->mergeCells('AI6:AK6');
                                }
                            }

                            if(count($this->namedays) == 36)
                            {
                                if($printCell === "AH" || $printCell === "AI" || $printCell === "AJ")
                                {
                                    $event->sheet->getColumnDimension('AH')->setWidth(8);
                                    $event->sheet->getColumnDimension('AI')->setWidth(8);
                                    $event->sheet->getColumnDimension('AJ')->setWidth(8);
                                    $event->sheet->getDelegate()->getStyle($printCell.$font_color_start)
                                        ->getNumberFormat()
                                        ->setFormatCode('0.0');
                                    $event->sheet->mergeCells('AH6:AJ6');
                                }
                            }

                            if(count($this->namedays) == 35)
                            {
                                if($printCell === "AG" || $printCell === "AH" || $printCell === "AI")
                                {
                                    $event->sheet->getColumnDimension('AG')->setWidth(8);
                                    $event->sheet->getColumnDimension('AH')->setWidth(8);
                                    $event->sheet->getColumnDimension('AI')->setWidth(8);
                                    $event->sheet->getDelegate()->getStyle($printCell.$font_color_start)
                                        ->getNumberFormat()
                                        ->setFormatCode('0.0');
                                    $event->sheet->mergeCells('AG6:AI6');
                                }
                            }

                            if(count($this->namedays) == 34)
                            {
                                if($printCell === "AF" || $printCell === "AG" || $printCell === "AH")
                                {
                                    $event->sheet->getColumnDimension('AF')->setWidth(8);
                                    $event->sheet->getColumnDimension('AG')->setWidth(8);
                                    $event->sheet->getColumnDimension('AH')->setWidth(8);
                                    $event->sheet->getDelegate()->getStyle($printCell.$font_color_start)
                                        ->getNumberFormat()
                                        ->setFormatCode('0.0');
                                    $event->sheet->mergeCells('AF6:AH6');
                                }
                            }
                        }
                    }
                    $font_color_start++;
                }
                    
                // merge cell
                $event->sheet->mergeCells('A6:A7');
                $event->sheet->mergeCells('B6:B7');
                $event->sheet->mergeCells('C6:C7');

                // wrap text
                $event->sheet->getCell('B6')->setValue("社員\n番号");
                $event->sheet->getStyle('B6:B7')->getAlignment()->setWrapText(true);
                for ($i = 8; $i < (count($this->csvArray) + 8); $i++)
                {
                    $event->sheet->getRowDimension($i)->setRowHeight(25);
                }
                $event->sheet->getStyle('A6:'.$printCell.(count($this->csvArray) + 7))->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getStyle('A6:'.$printCell.(count($this->csvArray) + 7))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('C8:C'.(count($this->csvArray) + 7))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $event->sheet->getStyle('A8:C'.(count($this->csvArray) + 7))->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                
                    if(count($this->namedays) == 37)
                    {
                        $event->sheet->mergeCells('AI4:AJ4');
                        $event->sheet->getDelegate()->setCellValue('AI4', '労働数日');
                        $event->sheet->getDelegate()->getStyle('AI4')->applyFromArray([
                            'font' => [
                                'name' =>  'ＭＳ Ｐゴシック',
                                'size' =>  11,
                            ]
                        ]);
    
                        $event->sheet->mergeCells('AK3:AK4');
                        $event->sheet->getDelegate()->setCellValue('AK3', count($this->namedays)-6);
                        $event->sheet->getDelegate()->getStyle('AK3')->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('BFBFBF');
                                
                     
                        $event->sheet->getStyle('AL8:AL'.(count($this->csvArray) + 7))->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $event->sheet->getStyle('AL8:AL'.(count($this->csvArray) + 7))->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        
                    }
                    else if(count($this->namedays) == 36)
                    {
                        $event->sheet->mergeCells('AH4:AI4');
                        $event->sheet->getDelegate()->setCellValue('AH4', '労働数日');
                        $event->sheet->getDelegate()->getStyle('AH4')->applyFromArray([
                            'font' => [
                                'name' =>  'ＭＳ Ｐゴシック',
                                'size' =>  11,
                            ]
                        ]);
    
                        $event->sheet->mergeCells('AJ3:AJ4');             
                        $event->sheet->getDelegate()->setCellValue('AJ3', count($this->namedays)-6);
                        $event->sheet->getDelegate()->getStyle('AJ3')->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('BFBFBF');
    
                        $event->sheet->getStyle('AK8:AK'.(count($this->csvArray) + 7))->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $event->sheet->getStyle('AK8:AK'.(count($this->csvArray) + 7))->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    
                    }
                    else if(count($this->namedays) == 35)
                    {
                        $event->sheet->mergeCells('AG4:AH4');
                        $event->sheet->getDelegate()->setCellValue('AG4', '労働数日');
                        $event->sheet->getDelegate()->getStyle('AG4')->applyFromArray([
                            'font' => [
                                'name' =>  'ＭＳ Ｐゴシック',
                                'size' =>  11,
                            ]
                        ]);
                               
                        $event->sheet->mergeCells('AI3:AI4'); 
                        $event->sheet->getDelegate()->setCellValue('AI3', count($this->namedays)-6);
                        $event->sheet->getDelegate()->getStyle('AI3')->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('BFBFBF');
    
                        $event->sheet->getStyle('AJ8:AJ'.(count($this->csvArray) + 7))->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $event->sheet->getStyle('AJ8:AJ'.(count($this->csvArray) + 7))->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                    else if(count($this->namedays) == 34)
                    {
                        $event->sheet->mergeCells('AF4:AH4');
                        $event->sheet->getDelegate()->setCellValue('AF4', '労働数日');
                        $event->sheet->getDelegate()->getStyle('AF4')->applyFromArray([
                            'font' => [
                                'name' =>  'ＭＳ Ｐゴシック',
                                'size' =>  11,
                            ]
                        ]);
    
                        $event->sheet->mergeCells('AH3:AH4'); 
                        $event->sheet->getDelegate()->setCellValue('AH3', count($this->namedays)-6);
                        $event->sheet->getDelegate()->getStyle('AH3')->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('BFBFBF');
    
                        $event->sheet->getStyle('AI8:AI'.(count($this->csvArray) + 7))->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $event->sheet->getStyle('AI8:AI'.(count($this->csvArray) + 7))->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }    

                    $event->sheet->getStyle('C'.(count($this->csvArray) + 9).':F'.(count($this->csvArray) + 13))->applyFromArray($styleArray);
                    $event->sheet->mergeCells('C'.(count($this->csvArray) + 9).':F'.(count($this->csvArray) + 9));
                    $event->sheet->mergeCells('D'.(count($this->csvArray) + 10).':F'.(count($this->csvArray) + 10));
                    $event->sheet->mergeCells('D'.(count($this->csvArray) + 11).':F'.(count($this->csvArray) + 11));
                    $event->sheet->mergeCells('D'.(count($this->csvArray) + 12).':F'.(count($this->csvArray) + 12));
                    $event->sheet->mergeCells('D'.(count($this->csvArray) + 13).':F'.(count($this->csvArray) + 13));


                    $event->sheet->styleCells(
                        'C'.(count($this->csvArray) + 9).':F'.(count($this->csvArray) + 13),
                        [
                            'font' => [
                                'name' =>  'ＭＳ Ｐゴシック',
                                'size' =>  9
                            ],                      
                        ]
                    ); 
                    $event->sheet->styleCells(
                        'D'.(count($this->csvArray) + 10).':F'.(count($this->csvArray) + 13),
                        [
                            'font' => [
                                'name' =>  'ＭＳ Ｐゴシック',
                                'size' =>  11,
                                'bold' => true
                            ],                      
                        ]
                    ); 
                    $event->sheet->getStyle( 'C'.(count($this->csvArray) + 9).':F'.(count($this->csvArray) + 9))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->getStyle( 'C'.(count($this->csvArray) + 9).':F'.(count($this->csvArray) + 9))->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $event->sheet->getStyle( 'D'.(count($this->csvArray) + 10).':F'.(count($this->csvArray) + 13))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->getStyle( 'D'.(count($this->csvArray) + 10).':F'.(count($this->csvArray) + 13))->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


                    $event->sheet->getStyle( 'C'.(count($this->csvArray) + 10).':C'.(count($this->csvArray) + 13))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $event->sheet->getStyle( 'C'.(count($this->csvArray) + 10).':C'.(count($this->csvArray) + 13))->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                
                    $event->sheet->getDelegate()->getStyle('C'.(count($this->csvArray) + 9))->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('BFBFBF');

                    $event->sheet->getCell('C'.(count($this->csvArray) + 9))->setValue("記号");

                    $event->sheet->getCell('C'.(count($this->csvArray) + 10))->setValue("出勤");
                    $event->sheet->getCell('C'.(count($this->csvArray) + 11))->setValue("半日出勤");
                    $event->sheet->getCell('C'.(count($this->csvArray) + 12))->setValue("欠勤");
                    $event->sheet->getCell('C'.(count($this->csvArray) + 13))->setValue("有給休暇");

                    $event->sheet->getCell('D'.(count($this->csvArray) + 10))->setValue("1");
                    $event->sheet->getCell('D'.(count($this->csvArray) + 11))->setValue("出勤時間/8");
                    $event->sheet->getCell('D'.(count($this->csvArray) + 12))->setValue("X");
                    $event->sheet->getDelegate()->getStyle('D'.(count($this->csvArray) + 12))->getFont()->
                    getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                    $event->sheet->getCell('D'.(count($this->csvArray) + 13))->setValue("Ｐ");




                    $event->sheet->setSelectedCells('D2');
                    return;
                   
            },
        ];
    }
            
    public function startCell(): string
    {
        return 'A6';
    }

    public function headings() : array
    {
      return  [$this->workdays,$this->namedays];
    }

    public function title(): string
    {
        return '勤怠管理表'.$this->year."-".$this->month;
    }

}  
                
            
    