<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateAcademicGroupAPIRequest;
use App\Http\Requests\API\UpdateAcademicGroupAPIRequest;
use App\Http\Resources\AcademicGroupResource;
use App\Models\Tenants\AcademicGroup;
use Illuminate\Http\JsonResponse;

class AcademicGroupAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $academicGroups = AcademicGroup::query()
            ->with(['grade', 'section', 'schoolCycle'])
            ->withCount('students')
            ->get();

        return $this->sendResponse(AcademicGroupResource::collection($academicGroups), 'Academic Groups retrieved successfully');
    }

    public function store(CreateAcademicGroupAPIRequest $request): JsonResponse
    {
        $academicGroup = AcademicGroup::create($request->all());
        $academicGroup->load(['grade', 'section', 'schoolCycle'])->loadCount('students');

        return $this->sendResponse(new AcademicGroupResource($academicGroup), 'Academic Group saved successfully');
    }

    public function show($id): JsonResponse
    {
        $academicGroup = AcademicGroup::query()
            ->with(['grade', 'section', 'schoolCycle'])
            ->withCount('students')
            ->find($id);

        if (empty($academicGroup)) {
            return $this->sendError('Academic Group not found');
        }

        return $this->sendResponse(new AcademicGroupResource($academicGroup), 'Academic Group retrieved successfully');
    }

    public function update($id, UpdateAcademicGroupAPIRequest $request): JsonResponse
    {
        $academicGroup = AcademicGroup::find($id);

        if (empty($academicGroup)) {
            return $this->sendError('Academic Group not found');
        }

        $academicGroup->fill($request->all());
        $academicGroup->save();
        $academicGroup->load(['grade', 'section', 'schoolCycle'])->loadCount('students');

        return $this->sendResponse(new AcademicGroupResource($academicGroup), 'AcademicGroup updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $academicGroup = AcademicGroup::find($id);

        if (empty($academicGroup)) {
            return $this->sendError('Academic Group not found');
        }

        $academicGroup->delete();

        return $this->sendSuccess('Academic Group deleted successfully');
    }
}
