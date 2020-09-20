<?php

namespace App\Exports;

use App\User;


use App\Salary;
use App\Employee;
use Carbon\Carbon;
use App\AttendDetail;
use \Maatwebsite\Excel\Sheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Barryvdh\Debugbar\Facade as Debugbar;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class PaySlipExport implements WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return User::all();
    // }

    // public function query()
    // {
    //     return null;
    // }

    public $salaryData = null;
    public $employee = null;
    public $empName;
    public $empId;
    public $trans_money;
    public $leaveData;

    public function __construct(Employee $employee, Salary $salary, $empName, $empId, $trans_money, $leaveData) 
    {
        // $this->printY = $printY;
        // $this->type = CAL_GREGORIAN;
        // $this->month = date(substr((string)$this->printY,4)); // Month ID, 1 through to 12.
        // $this->year = date(substr((string)$this->printY,0,4)); // Year in 4 digit 2009 format.
        // $this->day_count = cal_days_in_month($this->type, $this->month, $this->year); // G

        $this->employee = $employee;
        $this->salaryData = $salary;
        $this->empName = $empName;
        $this->empId = $empId;
        $this->trans_money = $trans_money;
        $this->leaveData = $leaveData;
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
                $event->sheet->getColumnDimension('B')->setWidth(30);
                $event->sheet->getColumnDimension('C')->setWidth(22);
                $event->sheet->getColumnDimension('D')->setWidth(30);

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
                $presentDays = $date->daysInMonth - $this->leaveData['paidLeave'] - $this->leaveData['absent'];
                $event->sheet->getDelegate()->setCellValue('D9', $presentDays);

                $event->sheet->getDelegate()->setCellValue('A10', 'Total Working Hours');
                $totalWorkingHours = $date->daysInMonth * 8;
                $event->sheet->getDelegate()->setCellValue('B10', $totalWorkingHours);

                $event->sheet->getDelegate()->setCellValue('C10', 'Present Hours');
                $presentHours = $totalWorkingHours - ($this->leaveData['paidLeave'] * 8) - ($this->leaveData['absent'] * 8);
                $event->sheet->getDelegate()->setCellValue('D10', $presentHours);

                $event->sheet->getDelegate()->setCellValue('A12', 'Late Coming (Hours)');
                $event->sheet->getDelegate()->setCellValue('B12', $this->leaveData['late_coming']);

                $event->sheet->getDelegate()->setCellValue('C12', 'Leaving Early (Hours)');
                $event->sheet->getDelegate()->setCellValue('B12', $this->leaveData['leaving_early']);

                $event->sheet->getDelegate()->setCellValue('A13', 'Half Day (AM)');
                $event->sheet->getDelegate()->setCellValue('B13', $this->leaveData['amAbsentCount']);

                $event->sheet->getDelegate()->setCellValue('C13', 'Half Day (PM)');
                $event->sheet->getDelegate()->setCellValue('D13', $this->leaveData['pmAbsentCount']);

                $event->sheet->getDelegate()->setCellValue('A14', 'Paid Leave (Days)');
                $event->sheet->getDelegate()->setCellValue('B14', $this->leaveData['paidLeave']);

                $event->sheet->getDelegate()->setCellValue('C14', 'Paid Leave (Hours)');
                $event->sheet->getDelegate()->setCellValue('D14', $this->leaveData['paidLeave'] * 8);

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




                $boldColumns = ['A4', 'A8', 'A15', 'A17', 'C17', 'A23'];

                foreach($boldColumns as $bold){
                    $event->sheet->getStyle($bold)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ]
                    ]);
                }


            },
        ];
    }
}
