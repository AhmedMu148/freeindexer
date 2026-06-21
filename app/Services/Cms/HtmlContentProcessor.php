<?php

namespace App\Services\Cms;

use App\Models\Section;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

class HtmlContentProcessor
{
    private const SHORTCODE_MAP = [
        'cms.cta' => 'components.cms.sections.cta',
        'cms.stats' => 'components.cms.sections.stats',
        'cms.hero' => 'components.cms.sections.hero',
        'cms.rich-text' => 'components.cms.sections.rich_text',
    ];

    public function process(string $html, array $context = []): string
    {
        // 1. Render Blade template (evaluates {{ $vars }} and blade directives)
        try {
            $html = Blade::render($html, $context);
        } catch (\Throwable $e) {
            Log::warning("Blade rendering in custom section failed: " . $e->getMessage(), [
                'exception' => $e,
                'html' => $html,
            ]);
            // Never 500, fallback to raw HTML for shortcode expansion
        }

        // 2. Expand Whitelisted [[shortcodes]]
        $html = preg_replace_callback('/\[\[([a-zA-Z0-9\.\-_]+)(?:\s+([^\]]+))?\]\]/', function ($matches) use ($context) {
            $name = $matches[1];
            $attributesString = $matches[2] ?? '';

            if (!isset(self::SHORTCODE_MAP[$name])) {
                return $matches[0]; // Leave unknown shortcodes untouched
            }

            $view = self::SHORTCODE_MAP[$name];
            $attributes = $this->parseAttributes($attributesString);

            // Fetch library section if key is specified
            $data = [];
            if (!empty($attributes['key'])) {
                $section = Section::where('key', $attributes['key'])->first();
                if ($section && $section->isPublished()) {
                    $data = $section->data ?? [];
                }
            } else {
                $data = $attributes;
            }

            $scope = array_merge($context, $data);

            try {
                return view($view, ['data' => $scope])->render();
            } catch (\Throwable $e) {
                Log::warning("Failed to render shortcode view '{$view}': " . $e->getMessage());
                return "<!-- Error rendering shortcode {$name} -->";
            }
        }, $html);

        return $html;
    }

    private function parseAttributes(string $attributesString): array
    {
        $attributes = [];
        if (empty($attributesString)) {
            return $attributes;
        }

        preg_match_all('/([a-zA-Z0-9\-_]+)\s*=\s*(?:["\']([^"\']*)["\']|([^\s]+))/', $attributesString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = $match[1];
            $value = $match[2] !== '' ? $match[2] : ($match[3] ?? '');
            $attributes[$key] = $value;
        }

        return $attributes;
    }
}
