<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('pym_subscriptions', function (Blueprint $table) {
      $table->id();
      $table->string('uid')->index();
      $table->string('gateway_id');
      $table->string('subscr_id');
      $table->unsignedBigInteger('plan_id')->nullable();
      $table->json('data')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('pym_subscriptions');
  }
};
