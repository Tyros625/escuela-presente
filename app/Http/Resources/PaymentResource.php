<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'preference_id' => $this->preference_id,
            'status' => $this->status,
            'payment_id' => $this->payment_id,
            'merchant_order_id' => $this->merchant_order_id,
            'payment_method' => $this->payment_method,
            'payment_type' => $this->payment_type,
            'amount' => $this->amount,
            'student_id' => $this->student_id,
            'student_name' => $this->student->last_name_father.' '.$this->student->last_name_mother.', '.$this->student->name,
            'student_grade_group' => $this->student->academicGroup->grade->description.' '.$this->student->academicGroup->section->description,
            'student_enrollment' => $this->student->enrollment,
            'student_photo' => $this->student->photo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
