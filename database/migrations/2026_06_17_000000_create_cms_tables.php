<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('status')->default('draft'); // draft | published
            $table->string('visibility')->default('public'); // public | private
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('source')->default('admin'); // admin | api
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->nullable();
            $table->string('name');
            $table->string('type');
            $table->string('status')->default('published'); // draft | published
            $table->json('data')->nullable();
            $table->longText('html_content')->nullable();
            $table->string('wrapper_class')->nullable();
            $table->string('anchor_id')->nullable();
            $table->string('source')->default('admin'); // admin | api
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->json('overrides')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->string('token_prefix');
            $table->json('abilities')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip')->nullable();
            $table->string('last_used_user_agent')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_api_tokens');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('pages');
    }
};
