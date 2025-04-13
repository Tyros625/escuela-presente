<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateStudentAPIRequest;
use App\Http\Requests\API\UpdateStudentAPIRequest;
use App\Http\Resources\StudentResource;
use App\Imports\StudentImport;
use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\Student;
use App\Models\Tenants\User;
use App\Notifications\NewRegisteredStudentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $students = Student::when($request->query('grade'), function ($query, $grade) {
            $query->grade($grade);
        })->when($request->query('group'), function ($query, $group) {
            $query->group($group);
        })->when($request->query('name'), function ($query, $name) {
            $query->name($name);
        })->when($request->query('enrollment'), function ($query, $enrollment) {
            $query->where('enrollment', $enrollment);
        })->get();

        return $this->sendResponse(
            StudentResource::collection($students),
            'Students retrieved successfully'
        );
    }

    public function store(CreateStudentAPIRequest $request): JsonResponse
    {
        $config = GeneralConfiguration::first();

        if ($config->plan['name'] === 'Gratis' && Student::count() > $config->plan['limit']) {
            return response()->json([
                'success' => true,
                'message' => "Llegó al límite de {$config->plan['limit']} alumnos registrados.",
            ], 200);
        }

        DB::beginTransaction();

        try {
            $student = Student::create($request->all());
            $student->academic()->create($request->academic);
            $student->relative()->create($request->relatives);
            $student->socioeconomic()->create($request->socioeconomics);
            $student->health()->create($request->healths);
            $config = GeneralConfiguration::first();
            $config->last_enrollment = intval($config->last_enrollment) + 1;
            $config->update();

            DB::commit();

            $user = User::find(1);
            $user->notify(new NewRegisteredStudentNotification($student));

            return $this->sendResponse(
                new StudentResource($student),
                'Student saved successfully'
            );
        } catch (\Exception $e) {
            DB::rollback();

            return $e->getMessage();
        }
    }

    public function show($enrollment): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        return $this->sendResponse(new StudentResource($student), 'Student retrieved successfully');
    }

    public function update($enrollment, UpdateStudentAPIRequest $request): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $student->fill($request->all());
        $student->save();

        $student->academic->update($request->academic);
        $student->relative->update($request->relatives);
        $student->socioeconomic->update($request->socioeconomics);
        $student->health->update($request->healths);

        return $this->sendResponse(new StudentResource($student), 'Student updated successfully');
    }

    public function destroy($enrollment): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $user = User::where('student_id', $student->id)->first();

        if ($user) {
            $user->delete();
        }

        $student->delete();

        return $this->sendSuccess('Student deleted successfully');
    }

    public function import(Request $request): JsonResponse
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

            $import = new StudentImport;
            $import->import($file);

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Datos importados correctamente',
                    'file' => $name,
                ]
            );
        }
    }

    public function curp($curp): JsonResponse
    {
        $student = Student::where('curp', $curp)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        return $this->sendResponse(new StudentResource($student), 'Student retrieved successfully');
    }

    public function id($id): JsonResponse
    {
        $student = Student::where('id', $id)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        return $this->sendResponse(new StudentResource($student), 'Student retrieved successfully');
    }
}
