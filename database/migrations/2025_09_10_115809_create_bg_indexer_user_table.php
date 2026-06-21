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
      Schema::create('bg_indexer_points', function (Blueprint $table) {
        $table->id();
        $table->string('uid');
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
      Schema::dropIfExists('bg_indexer_points');
    }
  };
