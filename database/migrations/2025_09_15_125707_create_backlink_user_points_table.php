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
    Schema::create('backlinks_points', function (Blueprint $table) {
      $table->id();
      $table->string('uid')->index();
      $table->unsignedInteger('points')->default(0);
      $table->unsignedInteger('used')->default(0);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('backlinks_points');
  }
};
