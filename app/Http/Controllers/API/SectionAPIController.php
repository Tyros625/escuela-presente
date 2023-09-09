<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateSectionAPIRequest;
use App\Http\Requests\API\UpdateSectionAPIRequest;
use App\Http\Resources\SectionResource;
use App\Models\Tenants\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $sections = Section::all();

        return $this->sendResponse(SectionResource::collection($sections), 'Sections retrieved successfully');
    }

    public function store(CreateSectionAPIRequest $request): JsonResponse
    {
        $section = Section::create($request->all());

        return $this->sendResponse(new SectionResource($section), 'Section saved successfully');
    }

    public function show($id): JsonResponse
    {
        $section = Section::find($id);

        if (empty($section)) {
            return $this->sendError('Section not found');
        }

        return $this->sendResponse(new SectionResource($section), 'Section retrieved successfully');
    }

    public function update($id, UpdateSectionAPIRequest $request): JsonResponse
    {
        $section = Section::find($id);

        if (empty($section)) {
            return $this->sendError('Section not found');
        }

        $section->fill($request->all());
        $section->save();

        return $this->sendResponse(new SectionResource($section), 'Section updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $section = Section::find($id);

        if (empty($section)) {
            return $this->sendError('Section not found');
        }

        $section->delete();

        return $this->sendSuccess('Section deleted successfully');
    }
}
