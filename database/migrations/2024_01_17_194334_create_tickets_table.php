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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->foreignId('status_id')->constrained('ticket_statuses');
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('type_id')->constrained('ticket_types');
            $table->foreignId('priority_id')->constrained('ticket_priorities');
            $table->integer('order')->default(0);
            $table->string('name');
            $table->string('code');
            $table->longText('content');
            $table->string('issue_link')->nullable();
            $table->string('pr_link')->nullable();
            $table->float('estimation')->nullable();
            $table->longText('attachments')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
