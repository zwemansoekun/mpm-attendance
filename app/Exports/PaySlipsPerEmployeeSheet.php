<?php

namespace App\Exports;

use App\User;
use App\Salary;
use Carbon\Carbon;
use App\Api_Employees;
use App\EmployeeDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;

class PaySlipsPerEmployeeSheet implements WithTitle,WithEvents
{
    private $employee;
    private $yearMonth;
    private $salaryData;
    private $trans_money;
    private $leaveData;
    private $empName;
    private $empId;

    public function __construct($yearMonth, $employee)
    {
        $this->yearMonth = str_replace("-","",$yearMonth);
        $this->employee  = $employee;

        $ym = str_replace("-","/",$yearMonth);
        $this->salaryData = Salary::select("*")->where("pay_month",$ym)->where('employee_id',$this->employee->emp_id)->first();
        $this->trans_money = EmployeeDetail::where("pay_month",$ym)->where('emp_id',$this->employee->emp_id)->first()->trans_money;

        //that is temporary code for nishimura's testing  
        //it will be use when stay at home is over.
        $content =  Api_Employees::where('id',$this->employee->emp_id)->firstOrFail(); //file_get_contents(env("MIX_APP_API_URL")."/employees/".$this->employee->emp_id);
        $empApiArray = json_decode($content, true);

        $this->empId = $empApiArray['employeeId'];
        $this->empName = $empApiArray['name'];

        //$ymStr = str_replace("-","",$yearMonth);
        $data = DB::select('select (amPaidCount+pmPaidCount)/2 as paidLeave,amAbsentCount , pmAbsentCount ,
            (amAbsentCount+pmAbsentCount)/2 as absent ,late_coming,leaving_early
            FROM
            (select sum(late_coming) late_coming, sum(leaving_early) leaving_early,emp_no from attend_details 
            where emp_no = :emp_no1 and EXTRACT(YEAR_MONTH FROM date) = :date1 group by emp_no) attend left join
            (select count(emp_no) amPaidCount ,emp_no from attend_details where emp_no = :emp_no2 
            and EXTRACT(YEAR_MONTH FROM date) = :date2 and am_leave = 1 group by emp_no) amPaid 
            on amPaid.emp_no = attend.emp_no left join
            (select count(emp_no) pmPaidCount, emp_no from attend_details where emp_no = :emp_no3
            and EXTRACT(YEAR_MONTH FROM date) = :date3 and pm_leave = 1 group by emp_no) pmPaid 
            on pmPaid.emp_no = attend.emp_no left join
            (select count(emp_no) amAbsentCount , emp_no  from attend_details where emp_no = :emp_no4
            and EXTRACT(YEAR_MONTH FROM date) = :date4 and am_leave = 2 group by emp_no) amAbsent 
            on amAbsent.emp_no = attend.emp_no left join 
            (select count(emp_no) pmAbsentCount, emp_no  from attend_details where emp_no = :emp_no5 
            and EXTRACT(YEAR_MONTH FROM date) = :date5 and pm_leave = 2 group by emp_no) pmAbsent 
            on pmAbsent.emp_no = attend.emp_no where attend.emp_no = :emp_no6 
            ',['date1' => $this->yearMonth,'date2' => $this->yearMonth,'date3' => $this->yearMonth,'date4' => $this->yearMonth,'date5' => $this->yearMonth,
            'emp_no1' => $this->employee->emp_id,'emp_no2' => $this->employee->emp_id,'emp_no3' => $this->employee->emp_id
            ,'emp_no4' => $this->employee->emp_id,'emp_no5' => $this->employee->emp_id,'emp_no6' => $this->employee->emp_id]);
 
            if($data != null){
                $this->leaveData = json_decode(json_encode($data[0]),true);
            }
        
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                // row height
                $rows = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 17, 18, 20, 21, 22, 23];
                foreach($rows as $row){
                    $event->sheet->getRowDimension($row)->setRowHeight(18);
                }
                $event->sheet->getRowDimension(16)->setRowHeight(30);
                $event->sheet->getRowDimension(19)->setRowHeight(30);

                //column width
                $event->sheet->getColumnDimension('A')->setWidth(22);
                $event->sheet->getColumnDimension('B')->setWidth(28);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(28);

                $cellRange = 'A1:D1'; // Company Name
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                
                $event->sheet->getDelegate()->mergeCells('A1:D1');
                $event->sheet->getDelegate()
                    ->getStyle('A1')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->setCellValue('A1', 'MANAGEMENT PARTNERS MYANMAR CO.,LTD.');
                
                $event->sheet->getDelegate()->getStyle('A1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F4B183');

                $now = Carbon::now();
                $event->sheet->getDelegate()->setCellValue('D3', 'Date-'. $now->format("yy/m/d"));

                //Employee Details
                $event->sheet->getDelegate()->mergeCells('A4:D4');
                $event->sheet->getDelegate()->setCellValue('A4', 'Employee Details');
                $event->sheet->getDelegate()->getStyle('A4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('BDD7EE');

                $event->sheet->getDelegate()->setCellValue('A5', 'Employee Name');
                $event->sheet->getDelegate()->setCellValue('B5', $this->empName);

                $event->sheet->getDelegate()->setCellValue('C5', 'Starting Date');
                $event->sheet->getDelegate()->setCellValue('D5',  Carbon::parse($this->employee->entry_date)->format('Y/m/d'));

                $event->sheet->getDelegate()->setCellValue('A6', 'Employee ID');
                $event->sheet->getDelegate()->setCellValue('B6', $this->empId);

                $event->sheet->getDelegate()->setCellValue('C6', 'Salary Month');
                $event->sheet->getDelegate()->setCellValue('D6', $this->salaryData->pay_month);

                $event->sheet->getDelegate()->mergeCells('A7:D7');
                //Attendance Details
                $event->sheet->getDelegate()->mergeCells('A8:D8');
                $event->sheet->getDelegate()->setCellValue('A8', 'Attendance Details');
                $event->sheet->getDelegate()->getStyle('A8')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('BDD7EE');

                $event->sheet->getDelegate()->setCellValue('A9', 'Total Working Days');

                $dateMonthArray = explode('/', $this->salaryData->pay_month);
                $year = $dateMonthArray[0];
                $month = $dateMonthArray[1];
                $date = Carbon::createFromDate($year, $month, 1);
                $event->sheet->getDelegate()->setCellValue('B9', $date->daysInMonth);

                $event->sheet->getDelegate()->setCellValue('C9', 'Present Days');
                $presentDays = 0;
                $presentHours = 0;
                $totalWorkingHours = $date->daysInMonth * 8;
                if($this->leaveData != null){
                    
                    $presentDays = $date->daysInMonth - $this->leaveData['paidLeave'] - $this->leaveData['absent'];
                    $presentHours = $totalWorkingHours - ($this->leaveData['paidLeave'] * 8) - ($this->leaveData['absent'] * 8);

                    if($this->leaveData['late_coming'] != null){
                        $event->sheet->getDelegate()->setCellValue('B12', $this->leaveData['late_coming']);
                    }else{
                        $event->sheet->getDelegate()->setCellValue('B12', '-');
                    }

                    if($this->leaveData['leaving_early'] != null){
                        $event->sheet->getDelegate()->setCellValue('D12', $this->leaveData['leaving_early']);
                    }else{
                        $event->sheet->getDelegate()->setCellValue('D12', '-');
                    }

                    if($this->leaveData['amAbsentCount'] != null){
                        $event->sheet->getDelegate()->setCellValue('B13', $this->leaveData['amAbsentCount']);
                    }else{
                        $event->sheet->getDelegate()->setCellValue('B13', '-');
                    }

                    if($this->leaveData['pmAbsentCount'] != null){
                        $event->sheet->getDelegate()->setCellValue('D13', $this->leaveData['pmAbsentCount']);
                    }else{
                        $event->sheet->getDelegate()->setCellValue('D13', '-');
                    }
                    
                    if($this->leaveData['paidLeave'] != null){
                        $event->sheet->getDelegate()->setCellValue('B14', $this->leaveData['paidLeave']);
                        $event->sheet->getDelegate()->setCellValue('D14', $this->leaveData['paidLeave'] * 8);
                    }else{
                        $event->sheet->getDelegate()->setCellValue('B14', '-');
                        $event->sheet->getDelegate()->setCellValue('D14', '-');
                    }

                }else{
                    $event->sheet->getDelegate()->setCellValue('B12', '-');
                    $event->sheet->getDelegate()->setCellValue('D12', '-');
                    $event->sheet->getDelegate()->setCellValue('B13','-');
                    $event->sheet->getDelegate()->setCellValue('D13', '-');
                    $event->sheet->getDelegate()->setCellValue('B14', '-');
                    $event->sheet->getDelegate()->setCellValue('D14', '-');
                }
                //font align right
                $alignRightColumns = ['B12','D12', 'B13', 'D13', 'B14', 'D14'];

                foreach($alignRightColumns as $col){
                    $event->sheet->getDelegate()
                    ->getStyle($col)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                }

                $event->sheet->getDelegate()->setCellValue('D9', $presentDays);

                $event->sheet->getDelegate()->setCellValue('A10', 'Total Working Hours');
                
                $event->sheet->getDelegate()->setCellValue('B10', $totalWorkingHours);

                $event->sheet->getDelegate()->setCellValue('C10', 'Present Hours');
                $event->sheet->getDelegate()->setCellValue('D10', $presentHours);

                $event->sheet->getDelegate()->setCellValue('A12', 'Late Coming (Hours)');
                

                $event->sheet->getDelegate()->setCellValue('C12', 'Leaving Early (Hours)');
                

                $event->sheet->getDelegate()->setCellValue('A13', 'Half Day (AM)');
                

                $event->sheet->getDelegate()->setCellValue('C13', 'Half Day (PM)');
                

                $event->sheet->getDelegate()->setCellValue('A14', 'Paid Leave (Days)');
                

                $event->sheet->getDelegate()->setCellValue('C14', 'Paid Leave (Hours)');
                

                //Salary Details
                $event->sheet->getDelegate()->mergeCells('A15:D15');
                $event->sheet->getDelegate()->setCellValue('A15', 'Salary Details');
                $event->sheet->getDelegate()->getStyle('A15')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('BDD7EE');

                $event->sheet->getDelegate()->setCellValue('A16', 'Basic Salary');
                $event->sheet->getDelegate()->setCellValue('B16', $this->salaryData->income);

                $event->sheet->getDelegate()->setCellValue('C16', 'Transportation Allowance(per day)');
                $event->sheet->getStyle('C16')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->setCellValue('D16', $this->trans_money);

                $event->sheet->getDelegate()->mergeCells('A17:B17');
                $event->sheet->getDelegate()->setCellValue('A17', 'Earning');

                $event->sheet->getDelegate()->mergeCells('C17:D17');
                $event->sheet->getDelegate()->setCellValue('C17', 'Deduction');
                //underline
                $event->sheet->getStyle('A17:D17')->applyFromArray([
                    'font' => [
                        'underline' => true
                    ]
                ]);
               
                $event->sheet->getDelegate()->setCellValue('A18', 'Salary');
                $salaryAmt =   $this->salaryData->income - $this->salaryData->leave_late;
                $event->sheet->getDelegate()->setCellValue('B18', $salaryAmt);

                $event->sheet->getDelegate()->setCellValue('C18', 'Personal Income Tax');
                $event->sheet->getDelegate()->setCellValue('D18', $this->salaryData->income_tax);

                $event->sheet->getDelegate()->setCellValue('A19', 'Transporation Allowance(Total)');
                $event->sheet->getStyle('A19')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->setCellValue('B19', $this->salaryData->trans_money);

                $event->sheet->getDelegate()->setCellValue('C19', 'SSB');
                $event->sheet->getDelegate()->setCellValue('D19', $this->salaryData->ssb);

                $event->sheet->getDelegate()->setCellValue('A20', 'Japanese Allowance');
                $event->sheet->getDelegate()->setCellValue('B20', $this->salaryData->jlpt);

                $event->sheet->getDelegate()->setCellValue('A21', 'Yearly Bonus');
                $event->sheet->getDelegate()->setCellValue('B21', $this->salaryData->bonus);

                $event->sheet->getDelegate()->setCellValue('A22', 'Total');
                $earningTotal = $salaryAmt + $this->salaryData->trans_money +$this->salaryData->ssb + $this->salaryData->jlpt
                + $this->salaryData->bonus;
                $event->sheet->getDelegate()->setCellValue('B22', $earningTotal);

                $event->sheet->getDelegate()->setCellValue('C22', 'Total');
                $deductionTotal = $this->salaryData->income_tax + $this->salaryData->ssb ;
                $event->sheet->getDelegate()->setCellValue('D22', $deductionTotal);

                //Net Salary
                $event->sheet->getDelegate()->mergeCells('A23:B23');
                $event->sheet->getDelegate()->setCellValue('A23', 'Net Salary');
                $event->sheet->getDelegate()->getStyle('A23')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('BDD7EE');
                $event->sheet->getDelegate()
                    ->getStyle('A23')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->mergeCells('C23:D23');
                $event->sheet->getDelegate()->getStyle('C23')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('BDD7EE');
                $event->sheet->getDelegate()->setCellValue('C23', $this->salaryData->payment_amount);

                $event->sheet->getDelegate()->setCellValue('A25', 'Employer\'s Signature');
                $event->sheet->getDelegate()->setCellValue('A27', 'Employees signature');

                //font Bold
                $boldColumns = ['A1','A4', 'A8', 'A15', 'A17', 'C17', 'A23'];

                foreach($boldColumns as $bold){
                    $event->sheet->getStyle($bold)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ]
                    ]);
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

                //$sheet->setBorder('A1:F10', 'thin');
                $event->sheet->getStyle('A4:D23')->applyFromArray($styleArray);


            },
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return  $this->yearMonth . '_'. $this->empId . $this->empName;
    }
}