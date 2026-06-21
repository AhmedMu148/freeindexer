<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('files_list', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->foreignId('type_id')->constrained(table: 'files_list_type', column: 'id')->cascadeOnUpdate()->restrictOnDelete();
      $table->string('path');
      $table->integer('status')->default(1);
      $table->timestamps();
      $table->index('type_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('files_list');
  }
};
