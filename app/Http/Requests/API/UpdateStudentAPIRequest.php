<?php

namespace App\Http\Requests\API;

use App\Models\Tenants\Student;
use InfyOm\Generator\Request\APIRequest;

class UpdateStudentAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = Student::$rules;

        unset($rules['curp']);

        return $rules;
    }

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'last_name_father' => 'apellido paterno',
            'last_name_mother' => 'apellido materno',
            'nationality' => 'nacionalidad',
            'curp' => 'curp',
            'date_birth' => 'fecha de nacimiento',
            'place_birth' => 'lugar de nacimiento',
            'sex' => 'sexo',
            'weight' => 'peso',
            'height' => 'altura',
            'indigenous_group' => 'grupo indígena',
            'indigenous_language' => 'lengua indígena',
            'disability' => 'discapacidad',
            'health_insurance' => 'seguro médico',
            'scholarship' => 'beca',
            'address' => 'dirección',
            'colony' => 'colonia',
            'postal_code' => 'código postal',
            'municipality' => 'municipalidad',
            'federal_entity' => 'entidad federativa',
            'home_phone' => 'teléfono de casa',
            'email' => 'correo electrónico',
            'photo' => 'fotografía',

            'academic.udeei' => 'udeei',
            'academic.origin_school' => 'escuela de procedencia',
            'academic.federal_entity_school' => 'entidad federativa de la escuela',

            'relatives.student_live_with' => '¿Con quién vive?',

            'relatives.father_data.name' => 'nombre del padre',
            'relatives.father_data.email' => 'email del padre',
            'relatives.father_data.occupation' => 'ocupación del padre',
            'relatives.father_data.work_phone' => 'teléfono trabajo del padre',
            'relatives.father_data.relationship' => 'relación del padre',
            'relatives.father_data.work_address' => 'dirección trabajo del padre',
            'relatives.father_data.phone_whatsapp' => 'teléfono personal del padre',

            'relatives.mother_data.name' => 'nombre de la madre',
            'relatives.mother_data.email' => 'email de la madre',
            'relatives.mother_data.occupation' => 'ocupación de la madre',
            'relatives.mother_data.work_phone' => 'teléfono trabajo de la madre',
            'relatives.mother_data.relationship' => 'relación de la madre',
            'relatives.mother_data.work_address' => 'dirección trabajo de la madre',
            'relatives.mother_data.phone_whatsapp' => 'teléfono personal de la madre',
        ];
    }
}
