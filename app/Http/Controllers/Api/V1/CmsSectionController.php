<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Cms\UpsertSection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\SectionRequest;
use App\Http\Resources\Cms\SectionResource;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CmsSectionController extends Controller
{
    protected $upsertAction;

    public function __construct(UpsertSection $upsertAction)
    {
        $this->upsertAction = $upsertAction;
    }

    public function index(): AnonymousResourceCollection
    {
        $sections = Section::latest()->paginate(25);
        return SectionResource::collection($sections);
    }

    public function show(string $idOrKey): SectionResource
    {
        $section = Section::where('id', $idOrKey)
            ->orWhere('key', $idOrKey)
            ->firstOrFail();

        return new SectionResource($section);
    }

    public function store(SectionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $section = $this->upsertAction->execute($data, null, 'api');

        return (new SectionResource($section))
            ->response()
            ->setStatusCode(201);
    }

    public function update(SectionRequest $request, string $idOrKey): SectionResource
    {
        $section = Section::where('id', $idOrKey)
            ->orWhere('key', $idOrKey)
            ->firstOrFail();

        $data = $request->validated();
        $updatedSection = $this->upsertAction->execute($data, $section, 'api');

        return new SectionResource($updatedSection);
    }
}
