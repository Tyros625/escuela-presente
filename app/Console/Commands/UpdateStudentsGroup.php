<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Student;
use Illuminate\Console\Command;

class UpdateStudentsGroup extends Command
{
    protected $signature = 'update:group {tenant?}';

    protected $description = 'Actualizar Grupos de Estudiantes';

    public function handle()
    {
        if ($this->argument('tenant')) {
            $this->updateGroup(
                Tenant::find($this->argument('tenant'))
            );
        } else {
            Tenant::all()->each(
                function ($tenant) {
                    return $this->updateGroup($tenant);
                }
            );
        }
    }

    public function updateGroup($tenant)
    {
        $tenant->run(function () {
            $students = Student::with('academic')->get();
            $groups = AcademicGroup::with('grade', 'section', 'schoolCycle')->get();

            $students->each(function ($student) use ($groups) {
                $group = $groups->first(function ($group) use ($student) {
                    $grupo = $group->grade->description.$group->section->description.' '.$group->schoolCycle->description;
                    $estudiante = $student->academic->grade.$student->academic->group.' '.$student->academic->school_cycle;

                    return $grupo === $estudiante;
                });

                $student->academic_group_id = $group->id ?? null;
                $student->save();
            });

            return Command::SUCCESS;
        });
    }
}
