<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateSchoolCycleAPIRequest;
use App\Http\Requests\API\UpdateSchoolCycleAPIRequest;
use App\Http\Resources\SchoolCycleResource;
use App\Models\Tenants\SchoolCycle;
use Illuminate\Http\JsonResponse;

class SchoolCycleAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $schoolCicles = SchoolCycle::all();

        return $this->sendResponse(SchoolCycleResource::collection($schoolCicles), 'School Cicles retrieved successfully');
    }

    public function store(CreateSchoolCycleAPIRequest $request): JsonResponse
    {
        $schoolCycle = SchoolCycle::create($request->all());

        return $this->sendResponse(new SchoolCycleResource($schoolCycle), 'School Cicle saved successfully');
    }

    public function show($id): JsonResponse
    {
        $schoolCycle = SchoolCycle::find($id);

        if (empty($schoolCycle)) {
            return $this->sendError('School Cicle not found');
        }

        return $this->sendResponse(new SchoolCycleResource($schoolCycle), 'School Cicle retrieved successfully');
    }

    public function update($id, UpdateSchoolCycleAPIRequest $request): JsonResponse
    {
        $schoolCycle = SchoolCycle::find($id);

        if (empty($schoolCycle)) {
            return $this->sendError('School Cicle not found');
        }

        $schoolCycle->fill($request->all());
        $schoolCycle->save();

        return $this->sendResponse(new SchoolCycleResource($schoolCycle), 'SchoolCycle updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $schoolCycle = SchoolCycle::find($id);

        if (empty($schoolCycle)) {
            return $this->sendError('School Cicle not found');
        }

        $schoolCycle->delete();

        return $this->sendSuccess('School Cicle deleted successfully');
    }
}
