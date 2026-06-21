<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    protected $table = 'page_sections';

    protected $fillable = [
        'page_id',
        'section_id',
        'order',
        'is_visible',
        'overrides',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'overrides' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function resolvedData(): array
    {
        $sectionData = $this->section ? ($this->section->data ?? []) : [];
        $overrides = $this->overrides ?? [];

        return array_replace_recursive($sectionData, $overrides);
    }
}
