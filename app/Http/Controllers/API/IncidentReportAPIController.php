<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateIncidentReportAPIRequest;
use App\Http\Requests\API\UpdateIncidentReportAPIRequest;
use App\Http\Resources\IncidentReportResource;
use App\Models\Tenants\IncidentReport;
use App\Models\Tenants\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentReportAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $incidentReports = IncidentReport::when($request->query('student'), function ($query, $student) {
            $query->whereHas('student', function (Builder $query) use ($student) {
                $query->where('name', 'like', '%'.$student.'%')
                    ->orWhere('last_name_father', 'like', '%'.$student.'%')
                    ->orWhere('last_name_mother', 'like', '%'.$student.'%');
            });
        })->get();

        return $this->sendResponse(IncidentReportResource::collection($incidentReports), 'Incident Reports retrieved successfully');
    }

    public function store(CreateIncidentReportAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        // $photo = $request->file('photo');
        // $path = Helper::saveFileInLocal($photo, 'reports');
        // $input['photo'] = $path;

        $incidentReport = IncidentReport::create($input);

        return $this->sendResponse(new IncidentReportResource($incidentReport), 'Incident Report saved successfully');
    }

    public function show($enrollment): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $incidentReport = IncidentReport::where('student_id', $student->id)
            ->with('student', 'student.relative', 'incident', 'teacher', 'specialty')
            ->get();

        if (empty($incidentReport)) {
            return $this->sendError('Incident Report not found');
        }

        return response()->json([
            'success' => true,
            'data' => $incidentReport,
            'message' => 'Incident Report retrieved successfully',
        ]);
    }

    public function update($id, UpdateIncidentReportAPIRequest $request): JsonResponse
    {
        $incidentReport = IncidentReport::find($id);

        if (empty($incidentReport)) {
            return $this->sendError('Incident Report not found');
        }

        $incidentReport->fill($request->all());
        $incidentReport->save();

        return $this->sendResponse(new IncidentReportResource($incidentReport), 'IncidentReport updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $incidentReport = IncidentReport::find($id);

        if (empty($incidentReport)) {
            return $this->sendError('Incident Report not found');
        }

        $incidentReport->delete();

        return $this->sendSuccess('Incident Report deleted successfully');
    }

    public function id($id): JsonResponse
    {
        $student = Student::where('id', $id)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $incidentReport = IncidentReport::where('student_id', $student->id)
            ->with('student', 'student.relative', 'incident', 'teacher', 'specialty')
            ->latest()
            ->get();

        if (empty($incidentReport)) {
            return $this->sendError('Incident Report not found');
        }

        return response()->json([
            'success' => true,
            'data' => $incidentReport,
            'message' => 'Incident Report retrieved successfully',
        ]);
    }
}
