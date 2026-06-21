<?php

use App\Models\Page;
use App\Models\Section;
use App\Models\PageSection;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('public page renders only published and visible sections in order', function () {
    $page = Page::create([
        'title' => 'Test Page',
        'slug' => 'test-page',
        'status' => 'published',
        'visibility' => 'public',
    ]);

    // Section 1: published & visible, order 2
    $section1 = Section::create([
        'name' => 'Hero Section',
        'type' => 'hero',
        'status' => 'published',
        'data' => [
            'heading' => 'Hello from Section 1',
        ],
    ]);
    PageSection::create([
        'page_id' => $page->id,
        'section_id' => $section1->id,
        'order' => 2,
        'is_visible' => true,
    ]);

    // Section 2: published & visible, order 1 (should appear first)
    $section2 = Section::create([
        'name' => 'CTA Section',
        'type' => 'cta',
        'status' => 'published',
        'data' => [
            'heading' => 'Hello from Section 2',
        ],
    ]);
    PageSection::create([
        'page_id' => $page->id,
        'section_id' => $section2->id,
        'order' => 1,
        'is_visible' => true,
    ]);

    // Section 3: draft (should not render)
    $section3 = Section::create([
        'name' => 'Draft Section',
        'type' => 'rich_text',
        'status' => 'draft',
        'data' => [
            'heading' => 'Hello from Section 3',
        ],
    ]);
    PageSection::create([
        'page_id' => $page->id,
        'section_id' => $section3->id,
        'order' => 3,
        'is_visible' => true,
    ]);

    // Section 4: published but hidden on this page (should not render)
    $section4 = Section::create([
        'name' => 'Hidden Section',
        'type' => 'rich_text',
        'status' => 'published',
        'data' => [
            'heading' => 'Hello from Section 4',
        ],
    ]);
    PageSection::create([
        'page_id' => $page->id,
        'section_id' => $section4->id,
        'order' => 4,
        'is_visible' => false,
    ]);

    $response = $this->get('/pages/test-page');

    $response->assertStatus(200);
    $response->assertSee('Hello from Section 2'); // CTA Section (order 1)
    $response->assertSee('Hello from Section 1'); // Hero Section (order 2)
    $response->assertDontSee('Hello from Section 3'); // Draft Section
    $response->assertDontSee('Hello from Section 4'); // Hidden Section
});

test('per-page overrides apply correctly', function () {
    $page = Page::create([
        'title' => 'Test Page',
        'slug' => 'test-page',
        'status' => 'published',
        'visibility' => 'public',
    ]);

    $section = Section::create([
        'name' => 'Hero Section',
        'type' => 'hero',
        'status' => 'published',
        'data' => [
            'heading' => 'Original Heading',
            'subheading' => 'Original Subheading',
        ],
    ]);

    PageSection::create([
        'page_id' => $page->id,
        'section_id' => $section->id,
        'order' => 1,
        'is_visible' => true,
        'overrides' => [
            'heading' => 'Overridden Heading',
        ],
    ]);

    $response = $this->get('/pages/test-page');

    $response->assertStatus(200);
    $response->assertSee('Overridden Heading');
    $response->assertDontSee('Original Heading');
});

test('custom section without section tag gets wrapped', function () {
    $page = Page::create([
        'title' => 'Test Page',
        'slug' => 'test-page',
        'status' => 'published',
        'visibility' => 'public',
    ]);

    $section = Section::create([
        'name' => 'Custom Raw Section',
        'type' => 'custom',
        'status' => 'published',
        'html_content' => '<div class="raw-content">This is raw code.</div>',
        'wrapper_class' => 'my-wrapper-class',
        'anchor_id' => 'my-anchor',
    ]);

    PageSection::create([
        'page_id' => $page->id,
        'section_id' => $section->id,
        'order' => 1,
        'is_visible' => true,
    ]);

    $response = $this->get('/pages/test-page');

    $response->assertStatus(200);
    $response->assertSee('<section id="my-anchor" class="my-wrapper-class">', false);
    $response->assertSee('This is raw code.');
});

test('reserved slugs are rejected', function () {
    $page = Page::create([
        'title' => 'Admin Page',
        'slug' => 'admin',
        'status' => 'published',
    ]);

    $response = $this->get('/pages/admin');
    $response->assertStatus(404);
});
