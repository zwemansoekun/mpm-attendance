<?php

namespace App\Exports;

use App\AttendDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Barryvdh\Debugbar\Facade as Debugbar;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\Exportable;


class AttendDetailsExport implements FromCollection,WithEvents, WithCustomStartCell, WithHeadingRow,WithHeadings

{

    use Exportable;
    public $printY = 0;
    public $attendTime = null;
    public $workdays = array();
  

    public function __construct(int $printY) 
    {
        
        $this->printY = $printY;
    }

    // private $headings = [
   
    //     // 'emp_no', 
    //     // 'total_hours',
    //     // 'am1',
    //     // 'pm1'
    //     $this->workdays
    // ];

    public function collection()
    {
        $content =  file_get_contents("http://localhost:5000/employees");
        $empApiArray = json_decode($content, true);
        // 社員情報
        $empArray = array();
        for ($i = 0; $i < count($empApiArray); $i++) {
            $empSubArray = array();
            array_push($empSubArray, $i + 1);
            array_push($empSubArray, $empApiArray[$i]['employeeId']);
            array_push($empSubArray,  $empApiArray[$i]['name']);
            array_push($empArray,  $empSubArray);
        }

        //print_r($empArray); return;

        // 通勤情報
        $this->attendTime = DB::select('select date,emp_no,total_hours from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date order by emp_no,date asc', ['date' => $this->printY]);
       
        // 社員明細情報
        $empDetailArray = array();
        $empDetailArray = DB::select('select date,emp_no,total_hours from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date order by emp_no,date asc', ['date' => $this->printY]);
        
         return collect($this->attendTime);
    }

    public function map($attendTime): array
    {
        return [
            
            $attendTime->emp_no,
            $attendTime->total_hours,
            $attendTime->am1,
            $attendTime->pm1
        ];
    }
    

    
    /**
     * @return array
     */

    public function registerEvents(): array
    {
        return [
            BeforeExport::class    => function(BeforeExport $event) {
                // 
                $type = CAL_GREGORIAN;
                $month = date(substr((string)$this->printY,5)); // Month ID, 1 through to 12.
                $year = date(substr((string)$this->printY,0,3)); // Year in 4 digit 2009 format.
                $day_count = cal_days_in_month($type, $month, $year); // G
                for ($i = 1; $i <= $day_count; $i++) {
                    $this->workdays[] = $i;
                }
            },
            
            AfterSheet::class    => function(AfterSheet $event) {
                // 勤怠管理表
                $event->sheet->getDelegate()->mergeCells('D2:AB2');
                $event->sheet->getDelegate()
                    ->getStyle('D2')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->setCellValue('D2', $this->printY .'勤怠管理表');

                $cellRange = 'A4:E4'; // All headers
               
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                   ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                   ->getStartColor()->setARGB('FF17a2b8');
                
                
                
                   // font Size
                //$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);


              
                    //var_dump($workdays);

              
                   
                },
            ];
          }
          
          public function startCell(): string
          {
              return 'D5';
          }

          public function headings() : array
          {
      
              return  $this->workdays;
          }
          
        
}  
                
            
    




