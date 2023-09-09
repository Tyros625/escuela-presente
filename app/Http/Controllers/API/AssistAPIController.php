<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\AssistResource;
use App\Models\Tenants\Assist;
use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssistAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $assists = Assist::when($request->query('date_start'), function ($query, $date_start) {
            $query->whereDate('created_at', $date_start);
        })->when($request->query('grade'), function ($query, $grade) {
            $query->grade($grade);
        })->when($request->query('group'), function ($query, $group) {
            $query->group($group);
        })->get();

        return $this->sendResponse(AssistResource::collection($assists), 'Assists retrieved successfully');
    }

    public function store($enrollment): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $assists = Assist::where('student_id', $student->id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        if ($assists->toArray()) {
            return $this->sendError('Ya ha registrado asistencia el día de hoy');
        }

        $assist = Assist::create([
            'student_id' => $student->id,
        ]);

        return $this->sendResponse(new AssistResource($assist), 'Assist saved successfully');
    }

    public function show($enrollment): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $assists = Assist::where('student_id', $student->id)->get();

        if (empty($assists)) {
            return $this->sendError('Assists not found');
        }

        return response()->json([
            'success' => true,
            'data' => $assists,
            'message' => 'Assists saved successfully',
        ]);
    }

    public function exportPDF(Request $request)
    {
        $url = "https://quickchart.io/chart?c={type:'bar',data:{labels:[{$request->labelString}],datasets:[{label:'Asistencias',data:[{$request->datasetString}]}]}}";

        $config = GeneralConfiguration::first();

        $data = [
            'labels' => $request->labels,
            'dataset' => $request->dataset,
            'date' => $request->date,
            'grade' => $request->grade,
            'url' => $url,
            'logo' => $config->logo,
            'name' => $config->name,
            'cct' => $config->cct,
            'address' => $config->address,
        ];

        $pdf = Pdf::loadView('assist-report', $data);

        return $pdf->download('assist-report.pdf');
    }

    public function id($id): JsonResponse
    {
        $student = Student::where('id', $id)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $assists = Assist::where('student_id', $student->id)->latest()->get();

        if (empty($assists)) {
            return $this->sendError('Assists not found');
        }

        return response()->json([
            'success' => true,
            'data' => $assists,
            'message' => 'Assists saved successfully',
        ]);
    }
}
