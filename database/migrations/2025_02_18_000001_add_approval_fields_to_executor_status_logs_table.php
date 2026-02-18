<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('executor_status_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('executor_status_logs', 'approval_status')) {
                $table->string('approval_status')->nullable()->after('custom_note');
            }
            if (!Schema::hasColumn('executor_status_logs', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('executor_status_logs', 'approval_note')) {
                $table->text('approval_note')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('executor_status_logs', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_note');
            }
        });

        // Add foreign key and index separately (check if they exist)
        Schema::table('executor_status_logs', function (Blueprint $table) {
            // Only add foreign key if column exists and FK doesn't
            try {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            } catch (\Exception $e) {
                // FK already exists
            }

            try {
                $table->index('approval_status');
            } catch (\Exception $e) {
                // Index already exists
            }
        });
    }

    public function down(): void
    {
        Schema::table('executor_status_logs', function (Blueprint $table) {
            try { $table->dropForeign(['approved_by']); } catch (\Exception $e) {}
            try { $table->dropIndex(['approval_status']); } catch (\Exception $e) {}

            $cols = ['approval_status', 'approved_by', 'approval_note', 'approved_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('executor_status_logs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};