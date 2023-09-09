<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Tenants\IncidentReport;
use App\Models\Tenants\Student;
use App\Models\Tenants\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $students = Student::all();
        $teachers = Teacher::all();
        $incidents = IncidentReport::all();

        return response()->json([
            'students' => $students->count(),
            'teachers' => $teachers->count(),
            'incidents' => $incidents->count(),
        ]);
    }
}
