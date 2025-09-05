<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    DB::statement("ALTER TABLE cenas MODIFY COLUMN status ENUM('draft', 'published', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft'");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cenas', function (Blueprint $table) {
            //
        });
    }
};
