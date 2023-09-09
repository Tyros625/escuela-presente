<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateIncidentAPIRequest;
use App\Http\Requests\API\UpdateIncidentAPIRequest;
use App\Http\Resources\IncidentResource;
use App\Models\Tenants\Incident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $incidents = Incident::all();

        return $this->sendResponse(IncidentResource::collection($incidents), 'Incidents retrieved successfully');
    }

    public function store(CreateIncidentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $incident = Incident::create($input);

        return $this->sendResponse(new IncidentResource($incident), 'Incident saved successfully');
    }

    public function show($id): JsonResponse
    {
        $incident = Incident::find($id);

        if (empty($incident)) {
            return $this->sendError('Incident not found');
        }

        return $this->sendResponse(new IncidentResource($incident), 'Incident retrieved successfully');
    }

    public function update($id, UpdateIncidentAPIRequest $request): JsonResponse
    {
        $incident = Incident::find($id);

        if (empty($incident)) {
            return $this->sendError('Incident not found');
        }

        $incident->fill($request->all());
        $incident->save();

        return $this->sendResponse(new IncidentResource($incident), 'Incident updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $incident = Incident::find($id);

        if (empty($incident)) {
            return $this->sendError('Incident not found');
        }

        $incident->delete();

        return $this->sendSuccess('Incident deleted successfully');
    }
}
