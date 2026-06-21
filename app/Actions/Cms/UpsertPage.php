<?php

namespace App\Actions\Cms;

use App\Models\Page;
use Illuminate\Support\Facades\DB;

class UpsertPage
{
    public function execute(array $data, ?Page $page = null): Page
    {
        return DB::transaction(function () use ($data, $page) {
            if (!$page) {
                $page = new Page();
            }

            $page->fill([
                'slug' => $data['slug'] ?? $page->slug,
                'title' => $data['title'] ?? $page->title,
                'status' => $data['status'] ?? $page->status ?? 'draft',
                'visibility' => $data['visibility'] ?? $page->visibility ?? 'public',
                'seo_title' => $data['seo_title'] ?? $page->seo_title,
                'seo_description' => $data['seo_description'] ?? $page->seo_description,
                'seo_keywords' => $data['seo_keywords'] ?? $page->seo_keywords,
                'canonical_url' => $data['canonical_url'] ?? $page->canonical_url,
                'source' => $data['source'] ?? 'api',
            ]);

            $page->save();

            if (isset($data['sections']) && is_array($data['sections'])) {
                $page->pageSections()->delete();
                foreach ($data['sections'] as $item) {
                    $page->pageSections()->create([
                        'section_id' => $item['section_id'],
                        'order' => $item['order'] ?? 0,
                        'is_visible' => $item['is_visible'] ?? true,
                        'overrides' => $item['overrides'] ?? null,
                    ]);
                }
            }

            return $page;
        });
    }
}
