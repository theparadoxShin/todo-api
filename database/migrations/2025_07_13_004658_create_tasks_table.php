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
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');

            $table->boolean('is_completed')->default(false);
            $table->integer('priority')->default(0); // 0: low, 1: medium, 2: high
            $table->timestamp('due_date')->nullable();

            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();

            $table->index(['user_id', 'is_completed']);
            $table->index(['user_id', 'priority']);
            $table->index('is_completed');
            $table->index('priority');
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
