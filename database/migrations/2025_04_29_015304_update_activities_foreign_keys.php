<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['task_id']);
            
            // Add the new foreign key with cascade delete
            $table->foreign('task_id')
                  ->references('id')
                  ->on('tasks')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Drop the cascading foreign key
            $table->dropForeign(['task_id']);
            
            // Restore the original foreign key
            $table->foreign('task_id')
                  ->references('id')
                  ->on('tasks');
        });
    }
};