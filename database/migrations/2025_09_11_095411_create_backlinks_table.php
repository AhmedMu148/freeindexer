<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('backlinks_orders', function (Blueprint $table) {
      $table->id();
      $table->integer('uid');
      $table->string('ses_id');
      $table->string('project_name');
      $table->text('links');
      $table->text('keywords');
      $table->integer('qty');
      $table->string('report')->nullable();
      $table->integer('status_id')->default(1);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('backlinks_orders');
  }
};
