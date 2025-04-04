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
        Schema::create('project_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // Foreign key to projects table
            $table->string('text');
            $table->string('key_persons')->nullable();
            $table->string('status')->default('Pending');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->longtext('comments')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('file_upload')->nullable();
            $table->timestamps();

            // Add the foreign key constraint
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_details');
    }
};
