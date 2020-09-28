<?php

namespace App\Exports;

use DateTime;
use App\Salary;
use App\Setting;
use App\DelayTime;
use App\EmployeeDetail;

use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use App\Http\Resources\EmployeeDetail as EmployeeDetailResource;

class EngineerExport implements FromCollection,WithEvents,WithStrictNullComparison
{
    //FromCollection,ShouldAutoSize,WithStrictNullComparison,
    use Exportable;

    private $pay_month;
    private $salary=[];

    public function __construct($pay_month) 
    {
        $this->pay_month = $pay_month;    
    }

    public function collection()
    {
        $setting='';
     
        $employee =  file_get_contents("http://localhost:5000/employees");
        $empArray = json_decode($employee, true);
        $tem_emp=[];$tem_salary=[];      
        $global=Setting::select('money')->order_by('updated_at', 'desc')->first();//config('global');   //repair   

        $salary=Salary::with('ssbval')->where('pay_month',$this->pay_month)->get();
        $employdetail=EmployeeDetail::with('employee')->where('pay_month',$this->pay_month)->get();
        
        $setting=DelayTime::select('money')->where('month',$this->pay_month)->order_by('updated_at', 'desc')->first();//->where('create_month',$this->pay_month)->get();      

        if($setting->isEmpty()){
            $setting=$global;    
        }
      
        for ($i = 0; $i < count($empArray); $i++) 
        {
            $tem_emp[$empArray[$i]['id']]['emp_code']=$empArray[$i]['employeeId'];
            $tem_emp[$empArray[$i]['id']]['name']=$empArray[$i]['name'];
        }

        for ($i = 0; $i < count($salary); $i++) 
        {
            $tem_salary[$i]['emp_code']=$tem_emp[$salary[$i]['employee_id']]['emp_code'];
            $innerarray=(object)$employdetail[$i]->employee;
            $tem_salary[$i]['name']=$tem_emp[$salary[$i]['employee_id']]['name']."\n (".$innerarray->kana_name.")"; //$empdet;//['kana_name'];//$tem_emp[$salary[$i]['employee_id']]['name']."\n (".$empdet[$i]['kana_name'].")";
            $tem_salary[$i]['entry_date']=str_replace('-','/',$innerarray->entry_date);
            $tem_salary[$i]['income']=number_format($salary[$i]['income']);
            $tem_salary[$i]['trans_money']=number_format($salary[$i]['trans_money']);
            $tem_salary[$i]['jlpt']=number_format($salary[$i]['jlpt']);
            $tem_salary[$i]['bonus']=number_format($salary[$i]['bonus']);

            $before_deduction=(int)$salary[$i]['income']+(int)$salary[$i]['trans_money']+(int)$salary[$i]['bonus']+(int)$salary[$i]['jlpt'];
            
            $tem_salary[$i]['before_deduction']=number_format(((int)$salary[$i]['income']+(int)$salary[$i]['trans_money']+(int)$salary[$i]['bonus']+(int)$salary[$i]['jlpt']));
            $tem_salary[$i]['before_deduction_jpy']= number_format((float)($before_deduction/(int)$setting[0]['money']), 2, '.', ','); 
          
            $tem_salary[$i]['income_tax']=number_format($salary[$i]['income_tax']);
            $tem_salary[$i]['ssb']=number_format($salary[$i]['ssb']);
            $tem_salary[$i]['leave_late']=number_format($salary[$i]['leave_late']);

            $tem_salary[$i]['adju1']=number_format($salary[$i]['adju1']);
            $tem_salary[$i]['adju2']=number_format($salary[$i]['adju2']);
            $tem_salary[$i]['adju3']=number_format($salary[$i]['adju3']);

            $deduction_amount=(int)$salary[$i]['income_tax']+(int)$salary[$i]['ssb']+(int)$salary[$i]['leave_late']+(int)$salary[$i]['adju1']+$salary[$i]['adju2']+$salary[$i]['adju3'];            
            $tem_salary[$i]['payment_mm']=number_format($before_deduction- $deduction_amount);
            $payment_jpy=$before_deduction- $deduction_amount;
            $tem_salary[$i]['payment_jpy']= number_format((float)($payment_jpy/(int)$setting[0]['money']), 2, '.', ','); 

            $tem_salary[$i]['payment_jpy']= number_format((float)($payment_jpy/(int)$setting[0]['money']), 2, '.', ','); 
            $tem_salary[$i]['company_ssb']=number_format($salary[$i]->ssbval->c_paid);
            $company_ssb=$salary[$i]->ssbval->c_paid;
            $tem_salary[$i]['actual_ssb']=number_format($salary[$i]->ssbval->total_amount);
            $tem_salary[$i]['company_ssb_jpy']= number_format((float)($company_ssb/(int)$setting[0]['money']), 2, '.', ','); 
            
            $tem_salary[$i]['column_offset']=null;
            $tem_salary[$i]['position']=$employdetail[$i]['position'];
            $tem_salary[$i]['dob']=$innerarray->dob;

            $from = new DateTime($innerarray->dob);
            $to   = new DateTime('today');
            $age=$from->diff($to)->y;

            $tem_salary[$i]['age']=$age;
            $tem_salary[$i]['address']=$employdetail[$i]['address'];
            $tem_salary[$i]['phone_no']=$employdetail[$i]['phone_no'];
            $tem_salary[$i]['nrc_no']=$employdetail[$i]['nrc_no'];
            $tem_salary[$i]['bank_account']=$employdetail[$i]['bank_account'];
            $tem_salary[$i]['member']=$employdetail[$i]['member'];
            $tem_salary[$i]['child']=$employdetail[$i]['child'];
            $tem_salary[$i]['emg_ph_no']=$employdetail[$i]['emg_ph_no'];
            $tem_salary[$i]['waste_time']=$employdetail[$i]['waste_time'];
           
        }
        return collect($this->salary=$tem_salary);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                
                Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
                    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
                });

                $sheet = $event->sheet;
                $row = 1;
                $sheet->mergeCells('A1:B1');
                $sheet->styleCells(
                    'A1:B1',
                    [
                        'font' => [
                            'name' =>  'Arial',
                            'size' =>  11     
                        ],
                    ]
                );
                $sheet->styleCells(
                    'K6',
                    [
                        'font' => [
                            'name' =>  'Arial',
                            'size' =>  8    
                        ],
                    ]
                );
              
                $sheet->setCellValue('A1',date("Y/m/d H:i"));
            
                $increasedMonth= date('Y/m/d', strtotime($this->pay_month."/08". ' + 1 month'));
               
                $sheet->styleCells(
                    'A2',
                    [
                        'font' => [
                            'name' =>  'ＭＳ Ｐゴシック',
                            'size' =>  18,   
                            'bold' =>  true  
                        ],                      
                    ]
                ); 
                     
                $sheet->setCellValue('A2',"海外エンジニアコスト一覧表(".$this->pay_month."分 ".$increasedMonth."支給)");
                $sheet->mergeCells('A3:A6');
                $sheet->setCellValue('A3',"従業員番号");
                $sheet->mergeCells('B3:B6');
                $sheet->setCellValue('B3',"名前");
                $sheet->mergeCells('C3:C6');
                $sheet->setCellValue('C3',"入社日");

                $sheet->mergeCells('D3:I3');
                $sheet->setCellValue('D3',"控除前");
                $sheet->mergeCells('D4:D6');
                $sheet->setCellValue('D4',"基本給");
                $sheet->mergeCells('E4:F4');
                $sheet->setCellValue('E4',"手当");
                $sheet->mergeCells('H4:I4');
                $sheet->setCellValue('H4',"合計");
               
                $sheet->mergeCells('E5:F5');
                $sheet->setCellValue('E5',"ミャンマー(MMK)");
                $sheet->mergeCells('G4:G6');
                $sheet->setCellValue('G4',"ボーナス");
                $sheet->mergeCells('H5:H6');
                $sheet->setCellValue('H5',"現地通貨");
                $sheet->mergeCells('I5:I6');
                $sheet->setCellValue('I5',"JPY");

                $sheet->setCellValue('E6',"通勤交通費");
                $sheet->setCellValue('F6',"JLPT");

                $sheet->mergeCells('J3:L3');   
                $sheet->mergeCells('J4:L4'); 
                $sheet->mergeCells('J5:J6');
                // $sheet->mergeCells('K5:K6');
                $sheet->mergeCells('L5:L6');

                $sheet->setCellValue('J3',"控除額");
                $sheet->setCellValue('J4',"ミャンマー(MMK)");
                $sheet->setCellValue('J5',"所得税");
                $sheet->setCellValue('K5',"SSB(0%or2%)");
                $sheet->setCellValue('K6',"所得300,000MMK以上は一律所得300,000MMKとみなす");
                $sheet->setCellValue('L5',"遅刻欠勤早退");

                $sheet->mergeCells('M3:O3');
                $sheet->setCellValue('M3',"その他調整");
                $sheet->mergeCells('M4:M6');
                $sheet->mergeCells('N4:N6');
                $sheet->mergeCells('O4:O6');

                $sheet->mergeCells('P3:Q3');
                $sheet->setCellValue('P3',"支給額");
                $sheet->mergeCells('P4:P6');
                $sheet->mergeCells('Q4:Q6');
                $sheet->setCellValue('P4',"現地通貨");
                $sheet->setCellValue('Q4',"JPY");

                $sheet->mergeCells('R3:T3');
                $sheet->setCellValue('R3',"会社負担");
                $sheet->setCellValue('R4',"ミャンマー(MMK)");
                $sheet->mergeCells('S4:T4');
                $sheet->setCellValue('S4',"合計");
                $sheet->mergeCells('S5:S6');
                $sheet->mergeCells('T5:T6');

                $sheet->setCellValue('R5',"社会保険");
                $sheet->setCellValue('R6',"SSB(3%or5%)");
                $sheet->setCellValue('S5',"現地通貨");
                $sheet->setCellValue('T5',"JPY");

                $sheet->mergeCells('V3:V6');
                $sheet->mergeCells('W3:W6');
                $sheet->mergeCells('X3:X6');
                $sheet->mergeCells('Y3:Y6');
                $sheet->mergeCells('Z3:Z6');
                $sheet->mergeCells('AA5:AA6');

                $sheet->mergeCells('AB3:AB6');
                $sheet->mergeCells('AC3:AC6');
                $sheet->mergeCells('AD3:AD6');
                $sheet->mergeCells('AE3:AE6');
                $sheet->mergeCells('AF3:AF6');

                $sheet->setCellValue('V3',"業務内容");
                $sheet->setCellValue('W3',"生年月日");
                $sheet->setCellValue('X3',"年齢");
                $sheet->setCellValue('Y3',"住所");
                $sheet->setCellValue('Z3',"電話番号");
                $sheet->setCellValue('AA3',"各種コード");
                $sheet->setCellValue('AA4',"ミャンマー");
                $sheet->setCellValue('AA5',"身分証番号");
                $sheet->setCellValue('AB3',"銀行口座番号");
                $sheet->setCellValue('AC3',"家族構成");
                $sheet->setCellValue('AD3',"配偶者の\n有無/子");
                $sheet->setCellValue('AE3',"緊急連絡先");
                $sheet->setCellValue('AF3',"通勤手段/\n時間（分）");            
              
                 $this->salary;
                 return;             
            },

            AfterSheet::class  => function(AfterSheet $event) {                
                $sheet = $event->sheet;
                $borderArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                $borderremoveArray = [
                    'borders' => [                      
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                            // 'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];

                $sheet->getRowDimension(2)->setRowHeight(19);  
                $sheet->getStyle('A3:T6')->applyFromArray($borderArray);
                $sheet->getStyle('V3:AF6')->applyFromArray($borderArray);

                $sheet->getStyle('K5:K6')->applyFromArray($borderremoveArray);
                $sheet->getColumnDimension('A')->setWidth(13.9);
                $sheet->getRowDimension(3)->setRowHeight(55);  
                $sheet->getRowDimension(4)->setRowHeight(14); 
                $sheet->getRowDimension(5)->setRowHeight(41); 
                $sheet->getRowDimension(6)->setRowHeight(42); 

                $sheet->getColumnDimension('B')->setWidth(32);
                $sheet->getColumnDimension('C')->setWidth(14.5);
                $sheet->getColumnDimension('D')->setWidth(15.3);
                $sheet->getColumnDimension('E')->setWidth(13.9);
                $sheet->getColumnDimension('F')->setWidth(13.9);
                $sheet->getColumnDimension('G')->setWidth(13.5);
                $sheet->getColumnDimension('H')->setWidth(13.5);
                $sheet->getColumnDimension('I')->setWidth(13.5);
                $sheet->getColumnDimension('J')->setWidth(16);
                $sheet->getColumnDimension('K')->setWidth(18);
                $sheet->getColumnDimension('L')->setWidth(16);
                $sheet->getColumnDimension('M')->setWidth(13.9);
                $sheet->getColumnDimension('N')->setWidth(13.9);
                $sheet->getColumnDimension('O')->setWidth(13.9);
                $sheet->getColumnDimension('P')->setWidth(13.9);
                $sheet->getColumnDimension('Q')->setWidth(13.9);
                $sheet->getColumnDimension('R')->setWidth(17);
                $sheet->getColumnDimension('S')->setWidth(13.9);
                $sheet->getColumnDimension('T')->setWidth(13.9);
                
                $sheet->getColumnDimension('V')->setWidth(17);
                $sheet->getColumnDimension('W')->setWidth(15);
                $sheet->getColumnDimension('X')->setWidth(8);
                $sheet->getColumnDimension('Y')->setWidth(35);
                $sheet->getColumnDimension('Z')->setWidth(18);
                $sheet->getColumnDimension('AA')->setWidth(35);
                $sheet->getColumnDimension('AB')->setWidth(35);
                $sheet->getColumnDimension('AC')->setWidth(12);
                $sheet->getColumnDimension('AD')->setWidth(12);
                $sheet->getColumnDimension('AE')->setWidth(18);
                $sheet->getColumnDimension('AF')->setWidth(18);



                $sheet->getDelegate()->getStyle('D3:I6')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F2DCDB');
                
                $sheet->getDelegate()->getStyle('J3:O6')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D7E4BD');

                $sheet->getDelegate()->getStyle('P3:Q6')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('DBEEF4');
                
                $sheet->getDelegate()->getStyle('R3:T6')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF200');

                $sheet->getDelegate()->getStyle('V3:AF6')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('D7E4BD');

                $this->sheetStyle($sheet,'A3');
                $this->sheetStyle($sheet,'B3');
                $this->sheetStyle($sheet,'C3');

                $this->sheetStyle($sheet,'D3');
                $this->sheetStyle($sheet,'E4');
                $this->sheetStyle($sheet,'H4');
                $this->sheetStyle($sheet,'E5');
                $this->sheetStyle($sheet,'D4');
                $this->sheetStyle($sheet,'E6');
                $this->sheetStyle($sheet,'F6');
                $this->sheetStyle($sheet,'G4');
                $this->sheetStyle($sheet,'H5');
                $this->sheetStyle($sheet,'I5');
                $this->sheetStyle($sheet,'J3');
                $this->sheetStyle($sheet,'J4');
                $this->sheetStyle($sheet,'J5');
                $this->sheetStyle($sheet,'K5');
                $this->sheetStyle($sheet,'K6');
                $this->sheetStyle($sheet,'L5');
                $this->sheetStyle($sheet,'M3');
                $this->sheetStyle($sheet,'P3');
                $this->sheetStyle($sheet,'P4');
                $this->sheetStyle($sheet,'Q4');
                $this->sheetStyle($sheet,'R3');
                $this->sheetStyle($sheet,'R4');
                $this->sheetStyle($sheet,'S4');
                $this->sheetStyle($sheet,'R5');
                $this->sheetStyle($sheet,'R6');
                $this->sheetStyle($sheet,'S5');
                $this->sheetStyle($sheet,'T5');
                $this->sheetStyle($sheet,'V3');
                $this->sheetStyle($sheet,'W3');
                $this->sheetStyle($sheet,'X3');
                $this->sheetStyle($sheet,'Y3');
                $this->sheetStyle($sheet,'Z3');
                $this->sheetStyle($sheet,'AA3');
                $this->sheetStyle($sheet,'AA4');
                $this->sheetStyle($sheet,'AA5');
                $this->sheetStyle($sheet,'AB3');
                $this->sheetStyle($sheet,'AC3');
                $this->sheetStyle($sheet,'AD3');
                $this->sheetStyle($sheet,'AE3');
                $this->sheetStyle($sheet,'AF3');          
                
                $sheet->getStyle("A7:T".(count($this->salary)+7-1) )->applyFromArray($borderArray);
                $sheet->getStyle("V7:AF".(count($this->salary)+7-1) )->applyFromArray($borderArray);
                for($i=0;$i<count($this->salary);$i++){

                    $sheet->getRowDimension(7+$i)->setRowHeight(57);  
                    $this->sheetStyle($sheet,'A'.(7+$i).":T".(7+$i));
                    $this->sheetStyle($sheet,'V'.(7+$i).":AF".(7+$i));
                }
                $sheet->setSelectedCells(null);
                return;
            }
        ];
    }

    public function sheetStyle($sheet,$val)    {
        $sheet->getStyle($val)->getAlignment()->setWrapText(true);
        $sheet->getStyle($val)->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($val)->getAlignment()
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }  
}