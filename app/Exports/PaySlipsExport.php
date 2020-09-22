<?php
namespace App\Exports;

use App\Exports\PaySlipsPerEmployeeSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PaySlipsExport implements WithMultipleSheets
{
    use Exportable;

    protected $yearMonth;
    protected $employees;
    
    public function __construct($yearMonth , $employees)
    {
        $this->yearMonth = $yearMonth;
        $this->employees = $employees;
        
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->employees as $employee) {
            $sheets[] = new PaySlipsPerEmployeeSheet($this->yearMonth, $employee);
        }

        return $sheets;
    }
}
