<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Cms\UpsertPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\PageRequest;
use App\Http\Resources\Cms\PageResource;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CmsPageController extends Controller
{
    protected $upsertAction;

    public function __construct(UpsertPage $upsertAction)
    {
        $this->upsertAction = $upsertAction;
    }

    public function index(): AnonymousResourceCollection
    {
        $pages = Page::latest()->paginate(25);
        return PageResource::collection($pages);
    }

    public function show(string $slug): PageResource
    {
        $page = Page::where('slug', $slug)
            ->with(['pageSections.section'])
            ->firstOrFail();

        return new PageResource($page);
    }

    public function store(PageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['source'] = 'api';

        $page = $this->upsertAction->execute($data);
        $page->load(['pageSections.section']);

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    public function update(PageRequest $request, string $slug): PageResource
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $data = $request->validated();
        $data['source'] = 'api';

        $updatedPage = $this->upsertAction->execute($data, $page);
        $updatedPage->load(['pageSections.section']);

        return new PageResource($updatedPage);
    }
}
