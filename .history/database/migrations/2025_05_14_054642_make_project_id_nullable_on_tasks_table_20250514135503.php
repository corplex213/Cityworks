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
        // Just make the column nullable
        $table->unsignedBigInteger('project_id')->nullable()->change();

        // If you want to add the foreign key constraint (optional, only if you want to enforce it)
        // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        // If you added the foreign key above, drop it here
        // $table->dropForeign(['project_id']);

        $table->unsignedBigInteger('project_id')->nullable(false)->change();
    });
}
};
