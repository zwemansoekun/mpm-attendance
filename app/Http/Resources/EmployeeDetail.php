<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return $request;
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'pay_month' => $this->pay_month,
            'salary_amount' => $this->salary_amount,
            'trans_money' => $this->trans_money,
            'jlpt' => $this->jlpt,
            'ssb' => $this->ssb,
            'position' => $this->position,
            'address' => $this->address,
            'phone_no' => $this->phone_no,
            'nrc_no' => $this->nrc_no,
            'bank_account' => $this->bank_account,
            'member' => $this->member,
            'child' => $this->child,
            'emg_ph_no' => $this->emg_ph_no,
            'waste_time' => $this->waste_time,
            'emp_id' => $this->emp_id,
            'kana_name' => $this->employee->kana_name,
            'entry_date' => $this->employee->entry_date,
            'dob' => $this->employee->dob,
        ];
    }
}
