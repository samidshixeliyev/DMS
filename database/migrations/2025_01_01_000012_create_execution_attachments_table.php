<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_act_id')->constrained('legal_acts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('no action');
            $table->unsignedBigInteger('status_log_id')->nullable();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();

            $table->foreign('status_log_id')->references('id')->on('executor_status_logs')->onDelete('no action');
            $table->index('legal_act_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('execution_attachments');
    }
};