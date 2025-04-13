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
            Teacher::create(
                [
                    'name' => $row['nombres'],
                    'last_name' => $row['apellidos'],
                    'date_birth' => $row['fecha_nacimiento'],
                    'sex' => $row['sexo'],
                    'email' => $row['correo_electronico'],
                    'phone' => $row['telefono'],
                    'address' => $row['direccion'],
                    'specialty_id' => $this->specialty[$row['especialidad']],
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
        // dd($data);

        $data['nombres'] = Helper::unaccent($data['nombres']);
        $data['apellidos'] = Helper::unaccent($data['apellidos']);
        $data['fecha_nacimiento'] = Date::excelToDateTimeObject($data['fecha_nacimiento'])->format('Y-m-d');
        $data['sexo'] = Helper::unaccent($data['sexo']);
        $data['correo_electronico'] = $data['correo_electronico'];
        $data['telefono'] = Helper::unaccent($data['telefono']);
        $data['direccion'] = Helper::unaccent($data['direccion']);
        $data['especialidad'] = Helper::unaccent($data['especialidad']);

        // dd($data);

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.nombres' => 'required',
            '*.apellidos' => 'required',
            '*.fecha_nacimiento' => 'required',
            '*.sexo' => 'required',
            '*.correo_electronico' => 'required|unique:teachers,email',
            '*.telefono' => 'required|unique:teachers,phone',
            '*.direccion' => 'required',
            '*.especialidad' => 'required',
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
