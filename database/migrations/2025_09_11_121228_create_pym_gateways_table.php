<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('pym_gateways', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('code')->unique();
      $table->boolean('one_time')->default(false);
      $table->boolean('subs')->default(false);
      $table->string('type')->nullable();
      $table->decimal('minimum', 12, 2)->nullable();
      $table->string('key_id')->nullable();
      $table->boolean('item_show')->default(false)->nullable();
      $table->json('data')->nullable();
      $table->json('details')->nullable();
      $table->json('countries')->nullable();
      $table->integer('increase')->default(0)->nullable();
      $table->integer('sort')->default(0)->nullable();
      $table->string('status')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('pym_gateways');
  }
};
