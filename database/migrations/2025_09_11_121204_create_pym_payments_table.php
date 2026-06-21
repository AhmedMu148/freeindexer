<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('pym_payments', function (Blueprint $table) {
      $table->id();
      $table->string('uid')->index();
      $table->unsignedBigInteger('plan_id')->nullable();
      $table->string('gateway_id');
      $table->string('product');
      $table->string('txn')->nullable();
      $table->decimal('amount', 12, 2);
      $table->unsignedBigInteger('currency_id')->nullable();
      $table->json('source_details')->nullable();
      $table->unsignedBigInteger('subscription_id')->nullable();
      $table->string('ref')->nullable();
      $table->string('status')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('pym_payments');
  }
};
