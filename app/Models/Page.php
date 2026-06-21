<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'status',
        'visibility',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'source',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    const RESERVED_SLUGS = [
        'admin', 'api', 'login', 'register', 'logout', 'pages', 'livewire', 'up',
        'pricing', 'buy-app', 'download-app', 'about', 'contact', 'feedback-app',
        'feedback', 'process', 'indexer', 'profile', 'billing', 'paypal',
        'background-indexer', 'email', 'dashboard'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($page) {
            if ($page->status === 'published') {
                if (is_null($page->published_at)) {
                    $page->published_at = now();
                }
            } elseif ($page->status === 'draft') {
                $page->published_at = null;
            }
        });
    }

    public function scopePublishedBySlug($query, string $slug)
    {
        return $query->where('slug', $slug)->where('status', 'published');
    }

    public function scopeWithPublicSections($query)
    {
        return $query->with([
            'pageSections' => function ($q) {
                $q->where('is_visible', true)
                  ->whereHas('section', function ($sq) {
                      $sq->where('status', 'published');
                  })
                  ->orderBy('order', 'asc');
            },
            'pageSections.section'
        ]);
    }

    public function pageSections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('order', 'asc');
    }
}
