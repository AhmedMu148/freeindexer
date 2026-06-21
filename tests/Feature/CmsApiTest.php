<?php

use App\Models\Page;
use App\Models\Section;
use App\Models\CmsApiToken;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('API returns 401 without valid token', function () {
    $response = $this->getJson('/api/v1/cms/pages');
    $response->assertStatus(401);
});

test('API returns 403 when token lacks appropriate scope', function () {
    $token = CmsApiToken::create([
        'name' => 'ReadOnly Token',
        'token_hash' => hash('sha256', 'cms_live_secret123'),
        'token_prefix' => 'cms_live_sec',
        'abilities' => ['cms.pages.read'],
        'is_active' => true,
    ]);

    // Try writing page with read-only token
    $response = $this->withHeader('X-CMS-Token', 'cms_live_secret123')
        ->postJson('/api/v1/cms/pages', [
            'title' => 'API Page',
            'slug' => 'api-page',
        ]);

    $response->assertStatus(403);
});

test('API allows access when token has cms.all scope', function () {
    $token = CmsApiToken::create([
        'name' => 'Admin Token',
        'token_hash' => hash('sha256', 'cms_live_secret123'),
        'token_prefix' => 'cms_live_sec',
        'abilities' => ['cms.all'],
        'is_active' => true,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer cms_live_secret123')
        ->postJson('/api/v1/cms/pages', [
            'title' => 'API Page',
            'slug' => 'api-page',
        ]);

    $response->assertStatus(201);
    $response->assertJsonPath('data.title', 'API Page');
    $response->assertJsonPath('data.slug', 'api-page');
});

test('static env token grants super admin cms.all access', function () {
    config(['services.cms.api_token' => 'env_secret_token_123']);

    $response = $this->withHeader('X-CMS-Token', 'env_secret_token_123')
        ->getJson('/api/v1/cms/pages');

    $response->assertStatus(200);
});

test('expired and revoked tokens are rejected with 401', function () {
    $expiredToken = CmsApiToken::create([
        'name' => 'Expired Token',
        'token_hash' => hash('sha256', 'cms_live_expired'),
        'token_prefix' => 'cms_live_exp',
        'abilities' => ['cms.all'],
        'is_active' => true,
        'expires_at' => now()->subMinutes(1),
    ]);

    $revokedToken = CmsApiToken::create([
        'name' => 'Revoked Token',
        'token_hash' => hash('sha256', 'cms_live_revoked'),
        'token_prefix' => 'cms_live_rev',
        'abilities' => ['cms.all'],
        'is_active' => false,
        'revoked_at' => now(),
    ]);

    $response1 = $this->withHeader('X-CMS-Token', 'cms_live_expired')
        ->getJson('/api/v1/cms/pages');
    $response1->assertStatus(401);

    $response2 = $this->withHeader('X-CMS-Token', 'cms_live_revoked')
        ->getJson('/api/v1/cms/pages');
    $response2->assertStatus(401);
});

test('token tracking stamps last_used attributes on successful request', function () {
    $token = CmsApiToken::create([
        'name' => 'Tracked Token',
        'token_hash' => hash('sha256', 'cms_live_tracking'),
        'token_prefix' => 'cms_live_tra',
        'abilities' => ['cms.pages.read'],
        'is_active' => true,
    ]);

    $this->withHeader('X-CMS-Token', 'cms_live_tracking')
        ->getJson('/api/v1/cms/pages');

    $token->refresh();
    expect($token->last_used_at)->not->toBeNull()
        ->and($token->last_used_ip)->toBe('127.0.0.1');
});

test('CRUD operations and exact JSON shapes', function () {
    $token = CmsApiToken::create([
        'name' => 'Full Token',
        'token_hash' => hash('sha256', 'cms_live_full'),
        'token_prefix' => 'cms_live_ful',
        'abilities' => ['cms.all'],
        'is_active' => true,
    ]);

    $section = Section::create([
        'name' => 'Hero template',
        'type' => 'hero',
        'status' => 'published',
        'data' => ['heading' => 'Original Header'],
    ]);

    // Create Page
    $response = $this->withHeader('X-CMS-Token', 'cms_live_full')
        ->postJson('/api/v1/cms/pages', [
            'title' => 'New Page via API',
            'slug' => 'new-page-api',
            'status' => 'published',
            'visibility' => 'public',
            'seo_title' => 'Page SEO Title',
            'sections' => [
                [
                    'section_id' => $section->id,
                    'order' => 1,
                    'is_visible' => true,
                    'overrides' => ['heading' => 'Custom Header via Override'],
                ]
            ]
        ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'data' => [
            'id', 'slug', 'title', 'status', 'visibility',
            'seo' => ['title', 'description', 'keywords', 'canonical_url'],
            'source', 'published_at', 'sections', 'created_at', 'updated_at'
        ]
    ]);

    $responseData = $response->json('data');
    expect($responseData['sections'])->toHaveCount(1)
        ->and($responseData['sections'][0]['section_id'])->toBe($section->id)
        ->and($responseData['sections'][0]['section']['content']['heading'])->toBe('Original Header');

    // Update Page
    $updateResponse = $this->withHeader('X-CMS-Token', 'cms_live_full')
        ->putJson('/api/v1/cms/pages/new-page-api', [
            'title' => 'Updated Title via API',
            'slug' => 'new-page-api',
            'status' => 'draft',
        ]);

    $updateResponse->assertStatus(200);
    $updateResponse->assertJsonPath('data.title', 'Updated Title via API');
    $updateResponse->assertJsonPath('data.status', 'draft');
});
