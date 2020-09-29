<?php

namespace App\Exports;

use App\AttendDetail;
use App\EmployeeDetail;
use \Maatwebsite\Excel\Sheet;
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

class AttendForMonthExport implements FromCollection,WithEvents, WithCustomStartCell ,WithHeadings,WithTitle

{
    use Exportable;
    public $printY = 0;
    public $heading1 = array();
    public $heading2 = array();
    public $csvArray = array();
    public $employee = "";
    public $type = null;
    public $month = null;
    public $year = null;
    public $day_count = null;
    public $dayArray = array();
    public $totalTime = 0.0;
    private $attendCount=0;

  
    public function __construct(String $employee,int $printY) 
    {
        $this->printY = $printY;
        $this->employee = $employee;
        $this->type = CAL_GREGORIAN;
        $this->month = date(substr((string)$this->printY,4)); // Month ID, 1 through to 12.
        $this->year = date(substr((string)$this->printY,0,4)); // Year in 4 digit 2009 format.
        $this->day_count = cal_days_in_month($this->type, $this->month, $this->year); // G
    }

    public function collection()
    {
        $attendTime = DB::select('select am1 ,pm2 ,total_hours ,date,
            am_leave, pm_leave
            from attend_details 
            where emp_no = :emp_no and 
            EXTRACT(YEAR_MONTH FROM date) = :date
            order by date', ['date' => $this->printY,'emp_no' => substr($this->employee,0,1)]);
        
        
        $attendTime = json_decode(json_encode($attendTime),true);
        $totalTime = 0.0;

        for ($i = 1; $i <= $this->day_count; $i++)
        {
            $daySubArray = array();
            $daySubArray["id"] = $i;
            $daySubArray["date"] = date_create($this->year.'-'.$this->month.'-'.$i)->format('Y-m-d');
            $daySubArray["in_time"] = "";
            $daySubArray["out_time"] = "";
            $daySubArray["lunch_time"] = "";
            $daySubArray["total_time"] = $this->clockalize(8);
            $daySubArray["ot_time"] = "";
            $daySubArray["ot_reason"] = "";
            array_push($this->dayArray,$daySubArray);
        }
        
        $j = 0; $minutes = 0; $attendCount=0;
        for ($i = 0; $i < $this->day_count; $i++)
        {
            if($j < count($attendTime ))
            {
                if($this->dayArray[$i]["date"] == $attendTime[$j]["date"])
                {
                    if($attendTime[$j]["am_leave"] == 1 || $attendTime[$j]["pm_leave"] == 1)
                    {
                        $this->dayArray[$i]["in_time"] =  "有休";
                        $this->dayArray[$i]["out_time"] =  "";
                        $this->dayArray[$i]["lunch_time"] =  "";
                    }
                    else
                    {
                        $this->dayArray[$i]["in_time"] =  substr((string)$attendTime[$j]["am1"],0,5);
                        $this->dayArray[$i]["out_time"] =  substr((string)$attendTime[$j]["pm2"],0,5);
                        if($this->dayArray[$i]["in_time"] ||  $this->dayArray[$i]["out_time"]){
                                $attendCount++;
                        }
                        if($this->clockalize($attendTime[$j]["total_hours"])!='00:00'){
                            $this->dayArray[$i]["lunch_time"] =  "01:00";
                        }                      
                    }
                    $this->dayArray[$i]["total_time"] =  $this->clockalize($attendTime[$j]["total_hours"]);
                   $j++;
                }
            }
          
           list($hour, $minute) = explode(':', $this->dayArray[$i]["total_time"]);
           $minutes += $hour * 60;
           $minutes += $minute;
        }
        $this->attendCount=$attendCount;
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        $this->totalTime = sprintf('%02d:%02d', $hours, $minutes);
       
        for ($i = 0; $i < $this->day_count; $i++)
        {
            unset($this->dayArray[$i]["date"]);
        }
      
        return collect($this->dayArray);
    }

  
    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            BeforeExport::class    => function(BeforeExport $event) {
                // 一番目見出し
                array_push($this->heading1, "日");
                array_push($this->heading1, "出勤時間");
                array_push($this->heading1, "退勤時間");
                array_push($this->heading1, "休　憩");
                array_push($this->heading1, "");
                array_push($this->heading1, "");
                array_push($this->heading1, "備　考");
                // 二番目見出し
                array_push($this->heading2, "");
                array_push($this->heading2, "");
                array_push($this->heading2, "");
                array_push($this->heading2, "");
                array_push($this->heading2, "勤務時間");
                array_push($this->heading2, "残業時間");
                array_push($this->heading2, "残業理由");
            },

            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setWidth(5);
                $event->sheet->getColumnDimension('B')->setWidth(15);
                $event->sheet->getColumnDimension('C')->setWidth(15);
                $event->sheet->getColumnDimension('D')->setWidth(15);
                $event->sheet->getColumnDimension('E')->setWidth(15);
                $event->sheet->getColumnDimension('F')->setWidth(15);
                $event->sheet->getColumnDimension('G')->setWidth(20);
         
                $date = substr_replace((string)$this->printY.'月', '年', 4, 0);
                $event->sheet->getCell('A1')->setValue("出勤簿 ".$date);
                $event->sheet->getRowDimension(1)->setRowHeight(20);
                // $event->sheet->getDelegate()->getStyle('A1')->getFont()->setSize(15);
               
                // 支社長
                $event->sheet->getCell('E1')->setValue("支社長");
                $event->sheet->mergeCells('E2:E4');
                $event->sheet->mergeCells('A6:E6');
                
                $kanaName = DB::select('SELECT kana_name FROM employees WHERE emp_id = :empId', 
                    ['empId' => substr($this->employee,0,1)]);
                $kanaName = json_decode(json_encode($kanaName),true);
                $kanaName=isset($kanaName[0])?$kanaName[0]['kana_name']:'';


                $employdetail=EmployeeDetail::with('employee')->where('pay_month',$this->year."/".$this->month)->get();            
                if($employdetail->isNotEmpty()){
                    $trans_money=$employdetail[0]['trans_money'];
                }
                                                        
                $event->sheet->getCell('A6')->setValue("営業所名　MPミャンマー　　　　　　　  氏名".$kanaName);

                $event->sheet->getStyle('A6:E6')->applyFromArray([
                    'font' => [
                        'underline' => true
                    ]
                ]);

                $event->sheet->mergeCells('E8:F8');
                $event->sheet->getCell('E8')->setValue("実　働　時　間");

                Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
                    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
                });
                $event->sheet->styleCells(
                    'A1',
                    [
                        'font' => [
                            'name' =>  'ＭＳ Ｐゴシック',
                            'size' =>  18,   
                            'bold' =>  true  
                        ],                      
                    ]
                ); 
                $event->sheet->styleCells(
                    'E1',
                    [
                        'font' => [
                            'name' =>  'ＭＳ Ｐゴシック',
                            'size' =>  12     
                        ],
                    ]
                );
                $event->sheet->styleCells(
                    'A6:G50',
                    [
                        'font' => [
                            'name' =>  'ＭＳ Ｐゴシック',
                            'size' =>  12     
                        ],
                    ]
                );

                for($i = 8; $i <= count($this->dayArray) + 14; $i++)
                {
                   if($i>9){
                       $event->sheet->getRowDimension($i)->setRowHeight(19);
                   } 
                 
                   $event->sheet->styleCells(
                        'A'.$i.':G'.$i,
                        [
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            ]
                        ]
                    );
                }
                
                $dayArray = DB::select('select date from 
                    holidays where EXTRACT(YEAR_MONTH FROM date) = :date ', 
                    ['date' => $this->printY]);

                $dayArray = json_decode(json_encode($dayArray),true);
                $holidayArray = array();
                for($i = 0; $i < count($dayArray); $i++)
                {
                    array_push($holidayArray,$dayArray[$i]['date']);
                }
              
                $lineStart = 10;
                for ($i = 1; $i <= $this->day_count; $i++)
                {
                    $d_var = getdate(mktime(0,0,0,$this->month,$i,$this->year));
                    
                    $colorRange = 'A'.$lineStart .':G'.$lineStart;
                    if($d_var['weekday'] == "Saturday" || $d_var['weekday'] == "Sunday")
                    {
                        $event->sheet->getDelegate()->getStyle($colorRange)->getFill()
                                 ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                 ->getStartColor()->setARGB('C00000');//\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED
                        $event->sheet->getCell('B'.$lineStart)->setValue("");
                        $event->sheet->getCell('C'.$lineStart)->setValue("");
                        $event->sheet->getCell('D'.$lineStart)->setValue("");
                    }
                 
                    $day = strlen((string)$i);
                    if($day != 2)
                    {
                        $i = '0'. $i;
                    }
                    if (in_array($this->year.'-'.$this->month.'-'.$i, $holidayArray)) 
                    {
                        $event->sheet->getDelegate()->getStyle($colorRange)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FF228B22');
                        
                    }
                    $lineStart++;
                }
                
                $lineStart = 10;
                for ($i = 0; $i < count($this->dayArray); $i++)
                {
                    for ($j = 0; $j < count($this->dayArray[$i]); $j++)
                    {
                       if( $this->dayArray[$i]['in_time'] == '有休')
                        {
                            $event->sheet->getDelegate()->getStyle('B'.$lineStart)->getFont()->
                                getColor()->setARGB('C00000');
                            $event->sheet->getDelegate()->getStyle('E'.$lineStart)->getFont()->
                                getColor()->setARGB('C00000');
                        }
                    }
                    $lineStart++;
                }

                $event->sheet->mergeCells('A'.$lineStart.':D'.$lineStart);
                $event->sheet->getCell('A'.$lineStart)->setValue("勤　務　時　間　合　計");
                $event->sheet->getStyle('A'.$lineStart)->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY);

                $borderArray = [
                    'borders' => [
                        'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        ],
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        ],
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                        ],
                    ]
                            
                ];   
                // $tableBorderArray = [
                //     'borders' => [
                //         'outline' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                //             'color' => ['argb' => 'FF000000'],
                //         ],
                //         'inside' => [
                //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                //             'color' => ['argb' => 'FF000000'],
                //         ],
                //     ],
                // ];   

                $event->sheet->getStyle('A'.$lineStart.':D'.$lineStart)->applyFromArray($borderArray);
                $event->sheet->getStyle('E'.$lineStart)->applyFromArray($borderArray);
                $event->sheet->getStyle('F'.$lineStart)->applyFromArray($borderArray);
                $event->sheet->getStyle('G'.$lineStart)->applyFromArray($borderArray);
                // $event->sheet->getStyle('A8:G9')->applyFromArray($tableBorderArray);
                
                $event->sheet->getCell('E'.$lineStart)->setValue($this->totalTime);
                
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
                
                $event->sheet->getStyle('A8:G'.(count($this->dayArray) + 9))->applyFromArray($styleArray);
                $event->sheet->getStyle('E1:E4')->applyFromArray($styleArray);

                $lineStart = $lineStart + 2;
             
                $event->sheet->getCell('B'.$lineStart)->setValue("勤務日数");
                $this->sheetStyle($event->sheet,'B'.$lineStart.":B".($lineStart+1));                
                $event->sheet->getCell('B'.($lineStart+1))->setValue($this->attendCount."     日");                
                $event->sheet->getStyle('B'.($lineStart+1))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $event->sheet->getCell('D'.$lineStart)->setValue("一日交通費");           
                $this->sheetStyle($event->sheet,'D'.$lineStart.":D".($lineStart+1)); 
                if(!isset($trans_money)){
                    $trans_money='';
                    $total_trans='';
                }else{
                    $total_trans=$trans_money*$this->attendCount;
                }     
                $event->sheet->getCell('D'.($lineStart+1))->setValue($trans_money."チャット");
                $event->sheet->getStyle('D'.($lineStart+1))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $event->sheet->getCell('E'.$lineStart)->setValue("交通費合計");
                $this->sheetStyle($event->sheet,'E'.$lineStart.":E".($lineStart+1));      
                $event->sheet->getCell('E'.($lineStart+1))->setValue($total_trans."チャット");
                $event->sheet->getStyle('E'.($lineStart+1))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
               
                $lineFinish = $lineStart + 2;
                $event->sheet->mergeCells('B'.($lineStart+1).':B'.$lineFinish);
                $event->sheet->mergeCells('D'.($lineStart+1).':D'.$lineFinish);
                $event->sheet->mergeCells('E'.($lineStart+1).':E'.$lineFinish);

                $event->sheet->getStyle('B'.$lineStart.':B'.$lineFinish)->applyFromArray($styleArray);
                $event->sheet->getStyle('D'.$lineStart.':D'.$lineFinish)->applyFromArray($styleArray);
                $event->sheet->getStyle('E'.$lineStart.':E'.$lineFinish)->applyFromArray($styleArray);

                $event->sheet->getStyle('E1')->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $lineExtraFinish = $lineFinish + 2; 
                $event->sheet->getDelegate()->getStyle('A'.$lineExtraFinish)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('C00000');
           
                $event->sheet->getDelegate()->getStyle('A'.($lineExtraFinish+1))->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('548235');
                
                $event->sheet->getCell('B'.$lineExtraFinish)->setValue("土日");
                $event->sheet->getCell('B'.($lineExtraFinish+1))->setValue("祝日");

                $event->sheet->mergeCells('A8:A9');    
                $event->sheet->mergeCells('B8:B9');   
                $event->sheet->mergeCells('C8:C9');
                $event->sheet->mergeCells('D8:D9');
                $event->sheet->mergeCells('E8:F8');


                $this->sheetStyle($event->sheet,'A8');
                $this->sheetStyle($event->sheet,'B8');
                $this->sheetStyle($event->sheet,'C8');
                $this->sheetStyle($event->sheet,'D8');
                $this->sheetStyle($event->sheet,'E8');
                $this->sheetStyle($event->sheet,'E9');
                $this->sheetStyle($event->sheet,'F9');
                $this->sheetStyle($event->sheet,'G8');
                $this->sheetStyle($event->sheet,'G9');

                $this->sheetStyle($event->sheet,'A10:G'.(count($this->dayArray) + 10));
                // $this->sheetStyle($event->sheet,'A'.(count($this->dayArray) + 10));
           




            }
        ];
    }
            
    public function startCell(): string
    {
        return 'A8';
    }

    public function headings() : array
    {
      return  [$this->heading1,$this->heading2];
    }

    public function clockalize($in){

        $h = intval($in);
        $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
        if ($m == 60)
        {
            $h++;
            $m = 0;
        }
        $retval = sprintf("%02d:%02d", $h, $m);
        return $retval;
    }

    public function title(): string
    {
        return  $this->month . '月 ';
    }

    public function sheetStyle($sheet,$val)  
    {
        $sheet->getStyle($val)->getAlignment()->setWrapText(true);
        $sheet->getStyle($val)->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($val)->getAlignment()
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }  
    
}  
                
            
    