<?php

namespace App\Services\Cms;

use App\Models\Section;
use App\Models\PageSection;

class SectionRenderer
{
    protected $processor;

    public function __construct(HtmlContentProcessor $processor)
    {
        $this->processor = $processor;
    }

    public function renderSections(iterable $pageSections, array $context = []): array
    {
        $rendered = [];

        foreach ($pageSections as $pageSection) {
            $section = $pageSection->section;
            if (!$section || !$section->isPublished()) {
                continue;
            }

            if (!array_key_exists($section->type, Section::TYPES)) {
                continue;
            }

            $scope = array_merge($context, $pageSection->resolvedData());
            $renderedSection = $this->renderSection($section, $scope, $pageSection->overrides);
            $rendered[] = $renderedSection;
        }

        return $rendered;
    }

    public function renderSection(Section $section, array $scope, ?array $overrides = null): RenderedSection
    {
        $html = '';
        if ($section->type === Section::TYPE_CUSTOM) {
            $rawHtml = $overrides['html_content'] ?? $section->resolvedHtmlContent();
            $processedHtml = $this->processor->process($rawHtml ?? '', $scope);

            if (!str_contains(strtolower($processedHtml), '<section')) {
                $processedHtml = view('cms.partials.section-wrapper', [
                    'html' => $processedHtml,
                    'wrapperClass' => $section->wrapper_class,
                    'anchorId' => $section->anchor_id,
                ])->render();
            }
            $html = $processedHtml;
        } else {
            $html = view("components.cms.sections.{$section->type}", ['data' => $scope])->render();
        }

        return new RenderedSection(
            $section->id,
            $section->name,
            $section->wrapper_class,
            $section->anchor_id,
            $html
        );
    }
}
