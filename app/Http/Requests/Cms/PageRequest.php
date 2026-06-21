<?php

namespace App\Http\Requests\Cms;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $page = $this->route('page');
        $pageId = null;
        if ($page instanceof Page) {
            $pageId = $page->id;
        } elseif (is_string($page)) {
            $pageRecord = Page::where('slug', $page)->first();
            if ($pageRecord) {
                $pageId = $pageRecord->id;
            }
        }

        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($pageId),
                Rule::notIn(Page::RESERVED_SLUGS),
            ],
            'status' => 'nullable|string|in:draft,published',
            'visibility' => 'nullable|string|in:public,private',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'sections' => 'nullable|array',
            'sections.*.section_id' => 'required|exists:sections,id',
            'sections.*.order' => 'nullable|integer|min:0',
            'sections.*.is_visible' => 'nullable|boolean',
            'sections.*.overrides' => 'nullable|array',
        ];
    }
}
