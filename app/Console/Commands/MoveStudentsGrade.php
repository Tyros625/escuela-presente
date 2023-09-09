<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Tenants\Student;
use Illuminate\Console\Command;

class MoveStudentsGrade extends Command
{
    protected $signature = 'move:grade {tenant?}';

    protected $description = 'Actualizar Grados de Estudiantes';

    public function handle()
    {
        if ($this->argument('tenant')) {
            $this->moveGrade(
                Tenant::find($this->argument('tenant'))
            );
        } else {
            Tenant::all()->each(
                function ($tenant) {
                    return $this->moveGrade($tenant);
                }
            );
        }
    }

    public function moveGrade($tenant)
    {
        $tenant->run(function () {
            $students = Student::with('academic')->get();

            $students->each(function ($student) {
                $grade = (int) $student->academic->grade;
                $student->academic->grade = $grade + 1;
                $student->academic->save();
            });

            return Command::SUCCESS;
        });
    }
}
