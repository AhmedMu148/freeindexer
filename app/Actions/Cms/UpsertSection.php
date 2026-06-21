<?php

namespace App\Models; // wait! namespace should be App\Actions\Cms!
// Let me correct that.
namespace App\Actions\Cms;

use App\Models\Section;

class UpsertSection
{
    public function execute(array $data, ?Section $section = null, string $source = 'api'): Section
    {
        if (!$section) {
            $section = new Section();
        }

        $section->fill([
            'key' => $data['key'] ?? $section->key,
            'name' => $data['name'] ?? $section->name,
            'type' => $data['type'] ?? $section->type,
            'status' => $data['status'] ?? $section->status ?? 'published',
            'data' => $data['data'] ?? $section->data,
            'html_content' => $data['html_content'] ?? $section->html_content,
            'wrapper_class' => $data['wrapper_class'] ?? $section->wrapper_class,
            'anchor_id' => $data['anchor_id'] ?? $section->anchor_id,
            'source' => $source,
        ]);

        $section->save();

        return $section;
    }
}
