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
            $table->id(); // Primary key
            $table->string('text'); // Text field
            $table->string('key_persons'); // Key persons field
            $table->string('status'); // Status field
            $table->date('start_date'); // Start date field
            $table->date('due_date'); // Due date field
            $table->text('comments')->nullable(); // Comments field (nullable)
            $table->string('file_upload')->nullable(); // File upload field (nullable)
            $table->decimal('budget', 10, 2); // Budget field
            $table->timestamps(); // Created at and updated at timestamps
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
