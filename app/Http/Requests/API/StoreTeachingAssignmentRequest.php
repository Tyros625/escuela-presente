<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeachingAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $days = config('teaching_schedule.days', []);
        $morning = config('teaching_schedule.morning_slots', []);
        $evening = config('teaching_schedule.evening_slots', []);
        $allSlots = array_merge($morning, $evening);

        return [
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'specialty_id' => ['required', 'integer', 'exists:specialties,id'],
            'academic_group_id' => ['required', 'integer', 'exists:academic_groups,id'],
            'shift' => ['required', Rule::in(['morning', 'afternoon'])],
            'day_of_week' => ['required', Rule::in($days)],
            'time_slot' => ['required', 'string', 'max:40', Rule::in($allSlots)],
            'assignment_type' => ['nullable', Rule::in(['manual', 'auto'])],
        ];
    }
}
