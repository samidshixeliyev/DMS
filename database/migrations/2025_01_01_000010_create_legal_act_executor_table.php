<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_act_executor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_act_id')->constrained('legal_acts')->onDelete('cascade');
            $table->foreignId('executor_id')->constrained('executors')->onDelete('cascade');
            $table->enum('role', ['main', 'helper'])->default('main');
            $table->timestamps();

            $table->unique(['legal_act_id', 'executor_id'], 'legal_act_executor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_act_executor');
    }
};
