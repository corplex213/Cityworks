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
        Schema::table('activities', function (Blueprint $table) {
            // Add a new column for target user details if not exists
            if (!Schema::hasColumn('activities', 'target_user_id')) {
                $table->unsignedBigInteger('target_user_id')->nullable();
                $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['target_user_id']);
            $table->dropColumn('target_user_id');
        });
    }
};
