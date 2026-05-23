<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateTeacherAPIRequest;
use App\Http\Requests\API\UpdateTeacherAPIRequest;
use App\Http\Resources\TeacherResource;
use App\Imports\TeacherImport;
use App\Models\Tenants\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $teachers = Teacher::query()
            ->with(['specialization', 'subject'])
            ->withCount([
                'teachingAssignments as assigned_hours_count' => function ($query) {
                    $query->where('is_active', true);
                },
            ])
            ->get();

        return $this->sendResponse(TeacherResource::collection($teachers), 'Teachers retrieved successfully');
    }

    public function store(CreateTeacherAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $teacher = Teacher::create($input);

        return $this->sendResponse(new TeacherResource($teacher), 'Teacher saved successfully');
    }

    public function show($id): JsonResponse
    {
        $teacher = Teacher::find($id);

        if (empty($teacher)) {
            return $this->sendError('Teacher not found');
        }

        return $this->sendResponse(new TeacherResource($teacher), 'Teacher retrieved successfully');
    }

    public function update($id, UpdateTeacherAPIRequest $request): JsonResponse
    {
        $teacher = Teacher::find($id);

        if (empty($teacher)) {
            return $this->sendError('Teacher not found');
        }

        $teacher->fill($request->all());
        $teacher->save();

        return $this->sendResponse(new TeacherResource($teacher), 'Teacher updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $teacher = Teacher::find($id);

        if (empty($teacher)) {
            return $this->sendError('Teacher not found');
        }

        $teacher->delete();

        return $this->sendSuccess('Teacher deleted successfully');
    }

    public function import(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|mimes:xls,xlsx',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if ($file = $request->file('file')) {
            $name = $file->getClientOriginalName();

            $import = new TeacherImport;
            $import->import($file);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Datos importados correctamente',
                    'file' => $name,
                ]
            );
        }
    }
}
