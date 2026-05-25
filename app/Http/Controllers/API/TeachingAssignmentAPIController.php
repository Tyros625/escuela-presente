<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\StoreTeachingAssignmentRequest;
use App\Http\Resources\TeachingAssignmentResource;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Teacher;
use App\Models\Tenants\TeachingAssignment;
use App\Services\ScheduleReportService;
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

    public function store(StoreTeachingAssignmentRequest $request, TeachingAssignmentValidator $validator): JsonResponse
    {
        $data = $request->validated();

        $teacher = Teacher::findOrFail($data['teacher_id']);
        $group = AcademicGroup::findOrFail($data['academic_group_id']);

        $error = $validator->validateNewAssignment(
            $teacher,
            $group,
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

        $ignoreAssignmentId = $assignment->id;
        if ((int) $data['teacher_id'] !== (int) $assignment->teacher_id) {
            $ignoreAssignmentId = null;
        }

        $error = $validator->validateNewAssignment(
            $teacher,
            $group,
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

    public function schedulePreviewPdf(Request $request, ScheduleReportService $reportService): Response
    {
        $shift = $request->get('shift', 'morning');
        if (! in_array($shift, ['morning', 'afternoon'], true)) {
            $shift = 'morning';
        }

        $data = $reportService->build($shift);

        $pdf = Pdf::loadView('teaching-schedule-report', $data)
            ->setPaper('legal', 'landscape');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="horario-docentes.pdf"',
        ]);
    }
}
