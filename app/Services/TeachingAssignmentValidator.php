<?php



namespace App\Services;



use App\Models\Tenants\AcademicGroup;

use App\Models\Tenants\GroupSubjectAssignment;

use App\Models\Tenants\Specialty;

use App\Models\Tenants\Teacher;

use App\Models\Tenants\TeachingAssignment;



class TeachingAssignmentValidator

{

    public function __construct(

        protected TeacherHoursService $hoursService

    ) {}



    public function validateNewAssignment(

        Teacher $teacher,

        AcademicGroup $group,

        Specialty $specialty,

        string $shift,

        string $dayOfWeek,

        string $timeSlot,

        ?int $ignoreAssignmentId = null

    ): ?string {

        if (! in_array($shift, ['morning', 'afternoon'], true)) {

            return 'Turno inválido.';

        }



        $days = config('teaching_schedule.days', []);

        if (! in_array($dayOfWeek, $days, true)) {

            return 'Día inválido.';

        }



        $allowedSlots = $shift === 'morning'

            ? config('teaching_schedule.morning_slots', [])

            : config('teaching_schedule.evening_slots', []);



        if (! in_array($timeSlot, $allowedSlots, true)) {

            return 'Hora inválida para el turno seleccionado.';

        }



        if ($group->shift !== $shift) {

            return 'El turno del grupo académico no coincide con el turno de la asignación.';

        }



        $subjectLinkError = $this->validateSubjectAssignmentLink($teacher, $group, $specialty);

        if ($subjectLinkError !== null) {

            return $subjectLinkError;

        }



        if (! $this->teacherHasSlotAvailability($teacher, $shift, $dayOfWeek, $timeSlot)) {

            return 'El docente no tiene disponibilidad en el horario seleccionado (día, turno y hora).';

        }



        if ($this->teacherHasConflict($teacher->id, $shift, $dayOfWeek, $timeSlot, $ignoreAssignmentId)) {

            return 'El docente ya tiene otra asignación en ese mismo horario.';

        }



        if (! SinGrupoAcademicGroupService::isSinGrupoGroup($group)

            && $this->groupHasConflict($group->id, $shift, $dayOfWeek, $timeSlot, $ignoreAssignmentId)) {

            return 'El grupo ya tiene otra materia asignada en ese mismo horario.';

        }



        $hoursError = $this->hoursService->validateCanTakeAssignment($teacher, $ignoreAssignmentId);

        if ($hoursError !== null) {

            return $hoursError;

        }



        return null;

    }



    protected function validateSubjectAssignmentLink(

        Teacher $teacher,

        AcademicGroup $group,

        Specialty $specialty

    ): ?string {

        if (TeachingScheduleRules::isServicioSpecialty($specialty)) {

            if (! SinGrupoAcademicGroupService::isSinGrupoGroup($group)) {

                return 'La materia SERVICIO debe asignarse al grupo SIN GRUPO.';

            }



            $hasServicioAssignment = GroupSubjectAssignment::query()

                ->where('teacher_id', $teacher->id)

                ->where('specialty_id', $specialty->id)

                ->where('school_cycle_id', $group->school_cycle_id)

                ->where('is_active', true)

                ->exists();



            if (! $hasServicioAssignment) {

                return 'El docente no tiene la materia SERVICIO asignada en Asignación de materias.';

            }



            return null;

        }



        if (SinGrupoAcademicGroupService::isSinGrupoGroup($group)) {

            return 'El grupo SIN GRUPO solo aplica a la materia SERVICIO.';

        }



        $hasAssignment = GroupSubjectAssignment::query()

            ->where('teacher_id', $teacher->id)

            ->where('specialty_id', $specialty->id)

            ->where('academic_group_id', $group->id)

            ->where('school_cycle_id', $group->school_cycle_id)

            ->where('is_active', true)

            ->exists();



        if (! $hasAssignment) {

            return 'El docente no tiene esta materia asignada a ese grupo en Asignación de materias.';

        }



        return null;

    }



    protected function teacherHasSlotAvailability(Teacher $teacher, string $shift, string $dayOfWeek, string $timeSlot): bool

    {

        $schedule = $teacher->schedule_availability;

        if (! is_array($schedule)) {

            return false;

        }



        $branch = $shift === 'morning' ? 'morning' : 'evening';



        if (isset($schedule[$branch][$timeSlot][$dayOfWeek]) && $schedule[$branch][$timeSlot][$dayOfWeek] === true) {

            return true;

        }



        if ($shift === 'morning') {

            $legacyMap = config('teaching_schedule.legacy_morning_slot_map', []);

            foreach ($legacyMap as $oldSlot => $newSlot) {

                if ($newSlot !== $timeSlot) {

                    continue;

                }

                if (isset($schedule['morning'][$oldSlot][$dayOfWeek]) && $schedule['morning'][$oldSlot][$dayOfWeek] === true) {

                    return true;

                }

            }

        }



        return false;

    }



    protected function teacherHasConflict(int $teacherId, string $shift, string $dayOfWeek, string $timeSlot, ?int $ignoreId): bool

    {

        $q = TeachingAssignment::query()

            ->where('is_active', true)

            ->where('teacher_id', $teacherId)

            ->where('shift', $shift)

            ->where('day_of_week', $dayOfWeek)

            ->where('time_slot', $timeSlot);



        if ($ignoreId !== null) {

            $q->where('id', '!=', $ignoreId);

        }



        return $q->exists();

    }



    protected function groupHasConflict(int $groupId, string $shift, string $dayOfWeek, string $timeSlot, ?int $ignoreId): bool

    {

        $q = TeachingAssignment::query()

            ->where('is_active', true)

            ->where('academic_group_id', $groupId)

            ->where('shift', $shift)

            ->where('day_of_week', $dayOfWeek)

            ->where('time_slot', $timeSlot);



        if ($ignoreId !== null) {

            $q->where('id', '!=', $ignoreId);

        }



        return $q->exists();

    }

}


