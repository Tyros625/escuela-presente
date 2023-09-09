<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateSpecialtyAPIRequest;
use App\Http\Requests\API\UpdateSpecialtyAPIRequest;
use App\Http\Resources\SpecialtyResource;
use App\Models\Tenants\Specialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpecialtyAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $specialties = Specialty::all();

        return $this->sendResponse(SpecialtyResource::collection($specialties), 'Specialties retrieved successfully');
    }

    public function store(CreateSpecialtyAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $specialty = Specialty::create($input);

        return $this->sendResponse(new SpecialtyResource($specialty), 'Specialty saved successfully');
    }

    public function show($id): JsonResponse
    {
        $specialty = Specialty::find($id);

        if (empty($specialty)) {
            return $this->sendError('Specialty not found');
        }

        return $this->sendResponse(new SpecialtyResource($specialty), 'Specialty retrieved successfully');
    }

    public function update($id, UpdateSpecialtyAPIRequest $request): JsonResponse
    {
        $specialty = Specialty::find($id);

        if (empty($specialty)) {
            return $this->sendError('Specialty not found');
        }

        $specialty->fill($request->all());
        $specialty->save();

        return $this->sendResponse(new SpecialtyResource($specialty), 'Specialty updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $specialty = Specialty::find($id);

        if (empty($specialty)) {
            return $this->sendError('Specialty not found');
        }

        $specialty->delete();

        return $this->sendSuccess('Specialty deleted successfully');
    }
}
