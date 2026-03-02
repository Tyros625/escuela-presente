<?php

namespace App\Imports;

use App\Helper;
use App\Models\Tenants\Specialty;
use App\Models\Tenants\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TeacherImport implements SkipsOnError, ToCollection, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable;

    private $specialty;

    public function __construct()
    {
        $this->specialty = Specialty::pluck('id', 'description');
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $lastNameFather = $row['apellido_paterno'] ?? $row['apellidos'] ?? null;
            $lastNameMother = $row['apellido_materno'] ?? null;

            Teacher::create(
                [
                    'name' => $row['nombres'],
                    'last_name' => $row['apellidos'] ?? null,
                    'last_name_father' => $lastNameFather,
                    'last_name_mother' => $lastNameMother,
                    'rfc' => $row['rfc'] ?? null,
                    'date_birth' => isset($row['fecha_nacimiento']) ? $row['fecha_nacimiento'] : null,
                    'sex' => $row['sexo'] ?? null,
                    'email' => $row['correo_electronico'] ?? null,
                    'institutional_email' => $row['correo_institucional'] ?? null,
                    'phone' => $row['telefono'] ?? null,
                    'address' => $row['direccion'] ?? null,
                    'specialty_id' => $this->specialty[$row['especialidad']] ?? null,
                    'max_hours_per_week' => $row['horas_maximas_semana'] ?? null,
                    'available_hours' => $row['horarios_disponibles'] ?? null,
                ]
            );
        }
    }

    public function batchSize(): int
    {
        return 4000;
    }

    public function chunkSize(): int
    {
        return 4000;
    }

    public function onError(\Throwable $e)
    {
        return $e;
    }

    public function prepareForValidation($data)
    {
        if (isset($data['nombres'])) {
            $data['nombres'] = Helper::unaccent($data['nombres']);
        }
        if (isset($data['apellidos'])) {
            $data['apellidos'] = Helper::unaccent($data['apellidos']);
        }
        if (isset($data['apellido_paterno'])) {
            $data['apellido_paterno'] = Helper::unaccent($data['apellido_paterno']);
        }
        if (isset($data['apellido_materno'])) {
            $data['apellido_materno'] = Helper::unaccent($data['apellido_materno']);
        }
        if (isset($data['fecha_nacimiento'])) {
            $data['fecha_nacimiento'] = Date::excelToDateTimeObject($data['fecha_nacimiento'])->format('Y-m-d');
        }
        if (isset($data['sexo'])) {
            $data['sexo'] = Helper::unaccent($data['sexo']);
        }
        if (isset($data['correo_electronico'])) {
            $data['correo_electronico'] = $data['correo_electronico'];
        }
        if (isset($data['telefono'])) {
            $data['telefono'] = Helper::unaccent($data['telefono']);
        }
        if (isset($data['direccion'])) {
            $data['direccion'] = Helper::unaccent($data['direccion']);
        }
        if (isset($data['especialidad'])) {
            $data['especialidad'] = Helper::unaccent($data['especialidad']);
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.nombres' => 'required',
            '*.apellido_paterno' => 'nullable',
            '*.apellido_materno' => 'nullable',
            '*.apellidos' => 'nullable',
            '*.fecha_nacimiento' => 'nullable',
            '*.sexo' => 'nullable',
            '*.correo_electronico' => 'nullable|unique:teachers,email',
            '*.correo_institucional' => 'nullable|unique:teachers,institutional_email',
            '*.telefono' => 'nullable|unique:teachers,phone',
            '*.direccion' => 'nullable',
            '*.especialidad' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nombres.required' => 'El atributo ":attribute" es obligatorio.',
            'apellidos.required' => 'El atributo ":attribute" es obligatorio.',
            'fecha_nacimiento.required' => 'El atributo ":attribute" es obligatorio.',
            'sexo.required' => 'El atributo ":attribute" es obligatorio.',
            'correo_electronico.required' => 'El atributo ":attribute" es obligatorio.',
            'correo_electronico.unique' => 'El atributo ":attribute" ya existe.',
            'phone.required' => 'El atributo ":attribute" es obligatorio.',
            'phone.unique' => 'El atributo ":attribute" ya existe.',
            'direccion.required' => 'El atributo ":attribute" es obligatorio.',
            'especialidad.required' => 'El atributo ":attribute" es obligatorio.',
        ];
    }
}
