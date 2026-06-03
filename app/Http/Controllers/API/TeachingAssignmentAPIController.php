<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\StoreTeachingAssignmentRequest;
use App\Http\Resources\TeachingAssignmentResource;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Specialty;
use App\Models\Tenants\Teacher;
use App\Models\Tenants\TeachingAssignment;
use App\Services\GroupSubjectAssignmentMatrixService;
use App\Services\ScheduleReportService;
use App\Services\SinGrupoAcademicGroupService;
use App\Services\TeachingAssignmentValidator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeachingAssignmentAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $rows = TeachingAssignment::query()
            ->where('is_active', true)
            ->with(['teacher', 'specialty', 'academicGroup.grade', 'academicGroup.section'])
            ->orderByDesc('id')
            ->get();

        return $this->sendResponse(
            TeachingAssignmentResource::collection($rows),
            'Teaching assignments retrieved successfully'
        );
    }

    public function formContext(
        Request $request,
        SinGrupoAcademicGroupService $sinGrupoService,
        GroupSubjectAssignmentMatrixService $matrixService
    ): JsonResponse {
        $schoolCycleId = (int) $request->get('school_cycle_id', 0);

        if ($schoolCycleId < 1) {
            return $this->sendError('Seleccione un período escolar.', 422);
        }

        $sinGrupoIds = $sinGrupoService->ensureForSchoolCycle($schoolCycleId);
        $matrix = $matrixService->build($schoolCycleId);

        $subjectLinks = [];
        foreach ($matrix['assignments'] as $key => $assignment) {
            if (empty($assignment['teacher_id'])) {
                continue;
            }

            $parsed = $matrixService->parseCellKey((string) $key);
            if ($parsed === null) {
                continue;
            }

            [$groupId, $specialtyId] = $parsed;

            $subjectLinks[] = [
                'teacher_id' => (int) $assignment['teacher_id'],
                'specialty_id' => $specialtyId,
                'academic_group_id' => $groupId,
            ];
        }

        return $this->sendResponse(
            [
                'school_cycle_id' => $schoolCycleId,
                'sin_grupo_group_ids' => $sinGrupoIds,
                'subject_links' => $subjectLinks,
            ],
            'Contexto del formulario recuperado correctamente'
        );
    }

    public function store(StoreTeachingAssignmentRequest $request, TeachingAssignmentValidator $validator): JsonResponse
    {
        $data = $request->validated();

        $teacher = Teacher::findOrFail($data['teacher_id']);
        $group = AcademicGroup::findOrFail($data['academic_group_id']);
        $specialty = Specialty::findOrFail($data['specialty_id']);

        $error = $validator->validateNewAssignment(
            $teacher,
            $group,
            $specialty,
            $data['shift'],
            $data['day_of_week'],
            $data['time_slot'],
            null
        );

        if ($error !== null) {
            return $this->sendError($error, 422);
        }

        $assignment = TeachingAssignment::create([
            'teacher_id' => $data['teacher_id'],
            'specialty_id' => $data['specialty_id'],
            'academic_group_id' => $data['academic_group_id'],
            'shift' => $data['shift'],
            'day_of_week' => $data['day_of_week'],
            'time_slot' => $data['time_slot'],
            'assignment_type' => $data['assignment_type'] ?? 'manual',
            'is_active' => true,
        ]);

        $assignment->load(['teacher', 'specialty', 'academicGroup.grade', 'academicGroup.section']);

        return $this->sendResponse(
            new TeachingAssignmentResource($assignment),
            'Asignación guardada correctamente'
        );
    }

    public function update($id, StoreTeachingAssignmentRequest $request, TeachingAssignmentValidator $validator): JsonResponse
    {
        $assignment = TeachingAssignment::find($id);

        if (empty($assignment)) {
            return $this->sendError('Asignación no encontrada');
        }

        $data = $request->validated();
        $teacher = Teacher::findOrFail($data['teacher_id']);
        $group = AcademicGroup::findOrFail($data['academic_group_id']);
        $specialty = Specialty::findOrFail($data['specialty_id']);

        $ignoreAssignmentId = $assignment->id;
        if ((int) $data['teacher_id'] !== (int) $assignment->teacher_id) {
            $ignoreAssignmentId = null;
        }

        $error = $validator->validateNewAssignment(
            $teacher,
            $group,
            $specialty,
            $data['shift'],
            $data['day_of_week'],
            $data['time_slot'],
            $ignoreAssignmentId
        );

        if ($error !== null) {
            return $this->sendError($error, 422);
        }

        $assignment->update([
            'teacher_id' => $data['teacher_id'],
            'specialty_id' => $data['specialty_id'],
            'academic_group_id' => $data['academic_group_id'],
            'shift' => $data['shift'],
            'day_of_week' => $data['day_of_week'],
            'time_slot' => $data['time_slot'],
        ]);

        $assignment->load(['teacher', 'specialty', 'academicGroup.grade', 'academicGroup.section']);

        return $this->sendResponse(
            new TeachingAssignmentResource($assignment),
            'Asignación actualizada correctamente'
        );
    }

    public function destroy($id): JsonResponse
    {
        $assignment = TeachingAssignment::find($id);

        if (empty($assignment)) {
            return $this->sendError('Asignación no encontrada');
        }

        $assignment->delete();

        return $this->sendSuccess('Asignación eliminada');
    }

    public function schedulePreview(Request $request, ScheduleReportService $reportService): JsonResponse
    {
        $shift = $this->resolveScheduleShift($request);

        return $this->sendResponse(
            $reportService->build($shift),
            'Vista previa de horario recuperada correctamente'
        );
    }

    public function schedulePreviewPdf(Request $request, ScheduleReportService $reportService): Response
    {
        $shift = $this->resolveScheduleShift($request);

        $data = $reportService->build($shift);

        $pdf = Pdf::loadView('teaching-schedule-report', $data)
            ->setPaper('legal', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="horario-docentes.pdf"',
        ]);
    }

    private function resolveScheduleShift(Request $request): string
    {
        $shift = $request->get('shift', 'morning');

        return in_array($shift, ['morning', 'afternoon'], true) ? $shift : 'morning';
    }
}
