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
    Schema::create('app_free', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('uid');
      $table->string('key', 155);
      $table->date('start_date');
      $table->date('end_date');
      $table->timestamps();
      $table->index('uid');
      $table->unique('key');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('app_free');
  }
};
