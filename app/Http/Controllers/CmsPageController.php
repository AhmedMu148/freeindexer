<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\Cms\SectionRenderer;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    protected $renderer;

    public function __construct(SectionRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function show(string $slug)
    {
        if (in_array(strtolower($slug), Page::RESERVED_SLUGS)) {
            abort(404);
        }

        $page = Page::publishedBySlug($slug)
            ->withPublicSections()
            ->firstOrFail();

        $renderedSections = $this->renderer->renderSections($page->pageSections, [
            'page' => $page,
        ]);

        return view('cms.show', compact('page', 'renderedSections'));
    }

    public function showAtRoot(Request $request)
    {
        $slug = $request->path();
        if ($slug === '/' || $slug === '') {
            $slug = 'home';
        }

        return $this->show($slug);
    }
}
