<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('ip_addresses', function (Blueprint $table) {
      $table->id();
      $table->string('ip')->index();
      $table->unsignedInteger('points')->default(0);
      $table->unsignedInteger('used')->default(0);
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('ip_addresses');
  }
};
