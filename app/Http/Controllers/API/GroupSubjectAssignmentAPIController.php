<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\BulkStoreGroupSubjectAssignmentRequest;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\GroupSubjectAssignment;
use App\Models\Tenants\Specialty;
use App\Models\Tenants\Teacher;
use App\Services\GroupSubjectAssignmentMatrixService;
use App\Services\GroupSubjectAssignmentValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupSubjectAssignmentAPIController extends AppBaseController
{
    public function matrix(Request $request, GroupSubjectAssignmentMatrixService $matrixService): JsonResponse
    {
        $schoolCycleId = (int) $request->get('school_cycle_id', 0);

        if ($schoolCycleId < 1) {
            return $this->sendError('Seleccione un período escolar.', 422);
        }

        return $this->sendResponse(
            $matrixService->build($schoolCycleId),
            'Matriz de asignación recuperada correctamente'
        );
    }

    public function bulkStore(
        BulkStoreGroupSubjectAssignmentRequest $request,
        GroupSubjectAssignmentValidator $validator
    ): JsonResponse {
        $schoolCycleId = (int) $request->validated('school_cycle_id');
        $changes = $request->validated('changes');
        $saved = 0;
        $cleared = 0;
        $warnings = [];

        foreach ($changes as $change) {
            $group = AcademicGroup::with('grade')->findOrFail($change['academic_group_id']);
            $specialty = Specialty::findOrFail($change['specialty_id']);

            if ((int) $group->school_cycle_id !== $schoolCycleId) {
                return $this->sendError(
                    "El grupo {$group->name} no pertenece al período escolar seleccionado.",
                    422
                );
            }

            if ($specialty->grade_id !== null && (int) $specialty->grade_id !== (int) $group->grade_id) {
                return $this->sendError(
                    "La materia {$specialty->description} no corresponde al grado del grupo.",
                    422
                );
            }

            $existing = GroupSubjectAssignment::query()
                ->where('academic_group_id', $group->id)
                ->where('specialty_id', $specialty->id)
                ->first();

            $teacherId = $change['teacher_id'] ?? null;

            if ($teacherId === null) {
                if ($existing !== null) {
                    $existing->delete();
                    $cleared++;
                }

                continue;
            }

            $teacher = Teacher::with('subject')->findOrFail($teacherId);

            $hoursError = $validator->validateTeacherHours(
                $teacher,
                $specialty,
                $existing?->id
            );

            if ($hoursError !== null) {
                return $this->sendError($hoursError, 422);
            }

            $subjectWarning = $validator->validateTeacherSubjectMatch($teacher, $specialty);
            if ($subjectWarning !== null) {
                $warnings[] = $subjectWarning;
            }

            GroupSubjectAssignment::updateOrCreate(
                [
                    'academic_group_id' => $group->id,
                    'specialty_id' => $specialty->id,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'school_cycle_id' => $schoolCycleId,
                    'assignment_type' => 'manual',
                    'is_active' => true,
                ]
            );

            $saved++;
        }

        $message = "Se guardaron {$saved} asignación(es).";
        if ($cleared > 0) {
            $message .= " Se quitaron {$cleared}.";
        }

        return $this->sendResponse(
            [
                'saved' => $saved,
                'cleared' => $cleared,
                'warnings' => array_values(array_unique($warnings)),
            ],
            $message
        );
    }
}
