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
        // Add minimal tracking fields to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('extends')->nullable()->index();
            $table->integer('billing_cycle_count')->default(0);
        });

        // pym_payments: add Central Payment tracking columns
        Schema::table('pym_payments', function (Blueprint $table) {
            $table->string('payment_hash')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
        });

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('event_id');
            $table->string('event_type');
            $table->json('payload');
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');

        Schema::table('pym_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_hash', 'metadata', 'completed_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['extends', 'billing_cycle_count']);
        });
    }
};
