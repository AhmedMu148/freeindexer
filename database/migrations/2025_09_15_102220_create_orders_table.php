<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

  public function up(): void
  {
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->string('uid')->index();
      $table->unsignedBigInteger('payment_id')->nullable();
      $table->unsignedBigInteger('subscription_id')->nullable();
      $table->unsignedBigInteger('plan_id')->nullable();
      $table->unsignedInteger('indexer')->default(0);
      $table->unsignedInteger('bg_indexer')->default(0);
      $table->unsignedInteger('backlinks')->default(0);
      $table->date('start')->nullable();
      $table->date('end')->nullable();
      $table->foreignId('status_id')->constrained('order_status');
      $table->timestamps();
      $table->index(['start', 'end']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
