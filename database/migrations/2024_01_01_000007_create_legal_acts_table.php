<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_acts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('act_type_id')->constrained('act_types')->onDelete('cascade');
            $table->foreignId('issued_by_id')->constrained('issuing_authorities')->onDelete('cascade');
            $table->foreignId('executor_id')->constrained('executors')->onDelete('cascade');
            $table->foreignId('execution_note_id')->nullable()->constrained('execution_notes')->onDelete('set null');
            $table->string('legal_act_number');
            $table->date('legal_act_date');
            $table->text('summary')->nullable();
            $table->string('task_number')->nullable();
            $table->text('task_description')->nullable();
            $table->date('execution_deadline');
            $table->string('related_document_number')->nullable();
            $table->date('related_document_date')->nullable();
            $table->string('created_by')->nullable();
            $table->date('created_date')->nullable();
            $table->foreignId('inserted_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_acts');
    }
};
