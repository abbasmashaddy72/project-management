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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->string('summary')->nullable();
            $table->integer('total');
            $table->integer('subtotal');
            $table->integer('vat')->nullable();
            $table->string('currency');
            $table->date('start');
            $table->date('end');
            $table->date('issued_on')->nullable();
            $table->date('due_on')->nullable();
            $table->date('paid_on')->nullable();
            $table->date('cancelled_on')->nullable();
            $table->date('reminded_on')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
