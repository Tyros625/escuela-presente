<?php

namespace App\Http\Requests\API;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Section;
use App\Services\AcademicGroupColorService;
use InfyOm\Generator\Request\APIRequest;

class UpdateAcademicGroupAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return AcademicGroup::$rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $grade = Grade::find($this->input('grade_id'));
            $sectionId = $this->input('section_id');
            $section = $sectionId ? Section::find($sectionId) : null;

            $error = AcademicGroupColorService::consistencyError(
                $this->input('name'),
                $grade,
                $section
            );

            if ($error !== null) {
                $validator->errors()->add('name', $error);
            }
        });
    }
}
