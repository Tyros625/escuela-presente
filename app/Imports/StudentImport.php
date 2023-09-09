<?php

namespace App\Imports;

use App\Helper;
use App\Models\Tenants\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class StudentImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, SkipsOnError
{
    use Importable;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Student::create(
                [
                    'enrollment' => $row['matricula'],
                    'name' => $row['nombre'],
                    'last_name_father' => $row['apellido_paterno'],
                    'last_name_mother' => $row['apellido_materno'],
                    'nationality' => $row['nacionalidad'],
                    'curp' => $row['curp'],
                    'date_birth' => $row['fecha_nacimiento'],
                    'place_birth' => $row['lugar_nacimiento'],
                    'sex' => $row['sexo'],
                    'weight' => $row['peso'],
                    'height' => $row['estatura'],
                    'is_migrant' => $row['migrante'],
                    'indigenous_group' => $row['grupo_indigena'],
                    'indigenous_language' => $row['lengua_indigena'],
                    'disability' => $row['discapacidad'],
                    'health_insurance' => $row['seguro_medico'],
                    'scholarship' => $row['beca'],
                    'address' => $row['domicilio'],
                    'colony' => $row['colonia'],
                    'postal_code' => $row['codigo_postal'],
                    'municipality' => $row['delegacion'],
                    'federal_entity' => $row['entidad_federativa'],
                    'home_phone' => $row['telefono_casa'],
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
        $fecha = Carbon::now()->toDateString();
        if ($data['fecha_nacimiento']) {
            $fecha = Date::excelToDateTimeObject($data['fecha_nacimiento'])->format('Y-m-d');
        }

        $data['matricula'] = Helper::unaccent($data['matricula']);
        $data['nombre'] = Helper::unaccent($data['nombre']);
        $data['apellido_paterno'] = Helper::unaccent($data['apellido_paterno']);
        $data['apellido_materno'] = Helper::unaccent($data['apellido_materno']);
        $data['nacionalidad'] = Helper::unaccent($data['nacionalidad']);
        $data['curp'] = Helper::unaccent($data['curp']);
        $data['fecha_nacimiento'] = $fecha;
        $data['lugar_nacimiento'] = Helper::unaccent($data['lugar_nacimiento']);
        $data['sexo'] = Helper::unaccent($data['sexo']);
        $data['peso'] = Helper::unaccent($data['peso']);
        $data['estatura'] = Helper::unaccent($data['estatura']);
        $data['migrante'] = $data['migrante'] == 'NO' ? false : true;
        $data['grupo_indigena'] = Helper::unaccent($data['grupo_indigena']);
        $data['lengua_indigena'] = Helper::unaccent($data['lengua_indigena']);
        $data['discapacidad'] = Helper::unaccent($data['discapacidad']);
        $data['seguro_medico'] = Helper::unaccent($data['seguro_medico']);
        $data['beca'] = Helper::unaccent($data['beca']);
        $data['domicilio'] = Helper::unaccent($data['domicilio']);
        $data['colonia'] = Helper::unaccent($data['colonia']);
        $data['codigo_postal'] = Helper::unaccent($data['codigo_postal']);
        $data['delegacion'] = Helper::unaccent($data['delegacion']);
        $data['entidad_federativa'] = Helper::unaccent($data['entidad_federativa']);
        $data['telefono_casa'] = Helper::unaccent($data['telefono_casa']);

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.matricula' => 'required|string|unique:students,enrollment',
            '*.nombre' => 'required',
            '*.apellido_paterno' => 'required',
            '*.apellido_materno' => 'required',
            '*.nacionalidad' => 'required',
            '*.curp' => 'required|string|unique:students,curp|size:18',
            '*.fecha_nacimiento' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'matricula.required' => 'El atributo ":attribute" es obligatorio.',
            'matricula.string' => 'El atributo ":attribute" debe ser una cadena de texto.',
            'matricula.unique' => 'El atributo ":attribute" ya existe.',
            'nombre.required' => 'El atributo ":attribute" es obligatorio.',
            'apellido_paterno.required' => 'El atributo ":attribute" es obligatorio.',
            'apellido_materno.required' => 'El atributo ":attribute" es obligatorio.',
            'nacionalidad.required' => 'El atributo ":attribute" es obligatorio.',
            'curp.required' => 'El atributo ":attribute" es obligatorio.',
            'curp.string' => 'El atributo ":attribute" debe ser una cadena de texto.',
            'curp.unique' => 'El atributo ":attribute" ya existe.',
            'curp.size' => 'El atributo ":attribute" debe tener 18 carácteres.',
            'fecha_nacimiento.required' => 'El atributo ":attribute" es obligatorio.',
        ];
    }
}
