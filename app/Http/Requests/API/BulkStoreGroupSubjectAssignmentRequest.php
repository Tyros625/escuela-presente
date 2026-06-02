<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreGroupSubjectAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_cycle_id' => ['required', 'integer', 'exists:school_cycles,id'],
            'changes' => ['required', 'array', 'min:1'],
            'changes.*.academic_group_id' => ['required', 'integer', 'exists:academic_groups,id'],
            'changes.*.specialty_id' => ['required', 'integer', 'exists:specialties,id'],
            'changes.*.teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
        ];
    }
}
