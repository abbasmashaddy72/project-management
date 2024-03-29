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
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epic_id')->nullable()->constrained('epics');
            $table->foreignId('project_id')->constrained('projects');
            $table->string('name');
            $table->dateTime('starts_at');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ends_at');
            $table->dateTime('ended_at')->nullable();
            $table->longText('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprints');
    }
};
