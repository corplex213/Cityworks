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
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('source_of_funding', ['DRRM-F', 'LDF', 'NTA', 'For funding', 'Others'])->nullable()->after('budget');
            $table->string('other_funding_source')->nullable()->after('source_of_funding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['source_of_funding', 'other_funding_source']);
        });
    }
}; 