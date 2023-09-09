<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateGradeAPIRequest;
use App\Http\Requests\API\UpdateGradeAPIRequest;
use App\Http\Resources\GradeResource;
use App\Models\Tenants\Grade;
use Illuminate\Http\JsonResponse;

class GradeAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $grades = Grade::all();

        return $this->sendResponse(GradeResource::collection($grades), 'Grades retrieved successfully');
    }

    public function store(CreateGradeAPIRequest $request): JsonResponse
    {
        $grade = Grade::create($request->all());

        return $this->sendResponse(new GradeResource($grade), 'Grade saved successfully');
    }

    public function show($id): JsonResponse
    {
        $grade = Grade::find($id);

        if (empty($grade)) {
            return $this->sendError('Grade not found');
        }

        return $this->sendResponse(new GradeResource($grade), 'Grade retrieved successfully');
    }

    public function update($id, UpdateGradeAPIRequest $request): JsonResponse
    {
        $grade = Grade::find($id);

        if (empty($grade)) {
            return $this->sendError('Grade not found');
        }

        $grade->fill($request->all());
        $grade->save();

        return $this->sendResponse(new GradeResource($grade), 'Grade updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $grade = Grade::find($id);

        if (empty($grade)) {
            return $this->sendError('Grade not found');
        }

        $grade->delete();

        return $this->sendSuccess('Grade deleted successfully');
    }
}
