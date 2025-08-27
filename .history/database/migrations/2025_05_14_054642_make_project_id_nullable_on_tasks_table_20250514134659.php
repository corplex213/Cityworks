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
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreignId('project_id')->constrained()->onDelete('cascade')->change();
        });
    }
};
