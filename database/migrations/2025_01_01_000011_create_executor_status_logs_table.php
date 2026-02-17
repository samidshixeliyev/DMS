<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executor_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_act_id')->constrained('legal_acts')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('execution_note_id');
            $table->text('custom_note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('execution_note_id')->references('id')->on('execution_notes')->onDelete('no action');
            $table->index(['legal_act_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executor_status_logs');
    }
};