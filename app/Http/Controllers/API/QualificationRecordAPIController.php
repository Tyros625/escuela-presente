<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Student;
use App\Models\Tenants\StudentGrade;
use App\Models\Tenants\StudentGradeHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Tenants\GeneralConfiguration;

class QualificationRecordAPIController extends AppBaseController
{
    public function groups(Request $request): JsonResponse
    {
        $query = AcademicGroup::with(['grade', 'section', 'schoolCycle']);

        // If the project later relates teachers to academic groups, this is
        // the place to filter by teacher_id. For now we just accept the param
        // and ignore it to avoid breaking existing data.
        if ($request->filled('teacher_id')) {
            // Placeholder for future teacher filtering.
        }

        $groups = $query->orderBy('name')->get();

        return $this->sendResponse($groups, 'Groups retrieved successfully');
    }

    public function groupGrades(AcademicGroup $group): JsonResponse
    {
        $students = Student::where('academic_group_id', $group->id)
            ->orderBy('last_name_father')
            ->orderBy('last_name_mother')
            ->orderBy('name')
            ->get();

        $grades = StudentGrade::where('academic_group_id', $group->id)
            ->get()
            ->keyBy('student_id');

        $studentItems = [];

        foreach ($students as $index => $student) {
            /** @var StudentGrade|null $grade */
            $grade = $grades->get($student->id);

            if (! $grade) {
                $grade = StudentGrade::create([
                    'student_id' => $student->id,
                    'academic_group_id' => $group->id,
                ]);
                $grades->put($student->id, $grade);
            }

            $listNumber = $index + 1;
            $name = trim($student->last_name_father.' '.$student->last_name_mother.', '.$student->name);

            $studentItems[] = [
                'list_number' => $listNumber,
                'student_id' => $student->id,
                'enrollment' => $student->enrollment,
                'name' => $name,
                'partial_1' => $grade?->partial_1,
                'partial_2' => $grade?->partial_2,
                'partial_3' => $grade?->partial_3,
                'average' => $grade?->average,
                'status' => $grade?->status,
                'grade_id' => $grade?->id,
            ];
        }

        $response = [
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'grade' => $group->grade?->description,
                'section' => $group->section?->description,
                'school_cycle' => $group->schoolCycle?->description,
            ],
            'students' => $studentItems,
        ];

        return $this->sendResponse($response, 'Group grades retrieved successfully');
    }

    public function updateGrade(Request $request, StudentGrade $grade): JsonResponse
    {
        $validated = $request->validate([
            'field' => 'required|in:partial_1,partial_2,partial_3',
            'value' => 'required|numeric|min:0|max:10',
            'reason' => 'required|string',
        ]);

        $field = $validated['field'];
        $newValue = (float) $validated['value'];
        $reason = $validated['reason'] ?? null;

        DB::transaction(function () use ($grade, $field, $newValue, $reason) {
            $oldValue = $grade->{$field};

            StudentGradeHistory::create([
                'student_grade_id' => $grade->id,
                'changed_at' => now(),
                'field_changed' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'reason' => $reason,
                'changed_by' => Auth::id(),
            ]);

            $grade->{$field} = $newValue;

            [$average, $status] = $this->computeGrade(
                $grade->partial_1,
                $grade->partial_2,
                $grade->partial_3
            );

            $grade->average = $average;
            $grade->status = $status;
            $grade->save();
        });

        $grade->refresh();

        return $this->sendResponse($grade, 'Grade updated successfully');
    }

    public function receipt(Request $request, StudentGrade $grade)
    {
        $grade->load(['student.academicGroup.grade', 'student.academicGroup.section', 'student.academicGroup.schoolCycle']);

        $payload = [
            'student' => [
                'id' => $grade->student->id,
                'enrollment' => $grade->student->enrollment,
                'name' => $grade->student->name,
                'last_name_father' => $grade->student->last_name_father,
                'last_name_mother' => $grade->student->last_name_mother,
            ],
            'group' => [
                'id' => $grade->academicGroup->id,
                'name' => $grade->academicGroup->name,
                'grade' => $grade->academicGroup->grade?->description,
                'section' => $grade->academicGroup->section?->description,
                'school_cycle' => $grade->academicGroup->schoolCycle?->description,
            ],
            'grades' => [
                'partial_1' => $grade->partial_1,
                'partial_2' => $grade->partial_2,
                'partial_3' => $grade->partial_3,
                'average' => $grade->average,
                'status' => $grade->status,
                'subject' => $grade->subject,
            ],
        ];

        if ($request->get('format') === 'pdf') {
            $generalConfiguration = GeneralConfiguration::first();

            $pdf = Pdf::loadView('qualification-receipt', [
                'data' => $payload,
                'generalConfiguration' => $generalConfiguration,
            ])->setPaper('letter');

            return $pdf->download('boleta-'.$grade->student->enrollment.'.pdf');
        }

        return $this->sendResponse($payload, 'Receipt retrieved successfully');
    }

    public function history(Request $request): JsonResponse
    {
        $request->validate([
            'group_id' => 'required|integer|exists:academic_groups,id',
        ]);

        $groupId = (int) $request->get('group_id');

        $histories = StudentGradeHistory::with([
            'studentGrade.student',
            'studentGrade.academicGroup',
            'user',
        ])->whereHas('studentGrade', function ($query) use ($groupId) {
            $query->where('academic_group_id', $groupId);
        })
            ->orderByDesc('changed_at')
            ->get();

        $items = $histories->map(function (StudentGradeHistory $history) {
            $student = $history->studentGrade->student;
            $group = $history->studentGrade->academicGroup;

            return [
                'changed_at' => $history->changed_at,
                'student_name' => trim($student->last_name_father.' '.$student->last_name_mother.', '.$student->name),
                'subject_name' => $history->studentGrade->subject,
                'field_changed' => $history->field_changed,
                'old_value' => $history->old_value,
                'new_value' => $history->new_value,
                'reason' => $history->reason,
                'changed_by' => $history->user?->name,
                'group_name' => $group->name,
            ];
        });

        return $this->sendResponse($items, 'History retrieved successfully');
    }

    /**
     * @return array{0: float|null, 1: string|null}
     */
    private function computeGrade(?float $p1, ?float $p2, ?float $p3): array
    {
        if ($p1 === null || $p2 === null || $p3 === null) {
            return [null, null];
        }

        $average = ($p1 + $p2 + $p3) / 3;
        $average = round($average, 1);

        $status = $average >= 6.0 ? 'approved' : 'failed';

        return [$average, $status];
    }
}

