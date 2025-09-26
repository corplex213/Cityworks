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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Links to users table
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->onDelete('cascade'); // Links to parent task for subtasks
            $table->string('task_name');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority', ['High', 'Normal', 'Low'])->default('Normal');
            $table->enum('status', ['Completed', 'For Checking', 'For Revision', 'Deferred'])->default('For Checking');
            $table->decimal('budget', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};