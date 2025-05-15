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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'reviewer_ids')) {
                $table->json('reviewer_ids')->nullable()->after('documentable_id');
            }
            if (!Schema::hasColumn('documents', 'approver_id')) {
                $table->unsignedBigInteger('approver_id')->nullable()->after('reviewer_ids');
            }
            if (!Schema::hasColumn('documents', 'preparer_id')) {
                $table->unsignedBigInteger('preparer_id')->nullable()->after('approver_id');
            }

            $table->foreign('approver_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('preparer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'reviewer_ids')) {
                $table->dropColumn('reviewer_ids');
            }
            if (Schema::hasColumn('documents', 'approver_id')) {
                $table->dropColumn('approver_id');
            }
            if (Schema::hasColumn('documents', 'preparer_id')) {
                $table->dropColumn('preparer_id');
            }
        });
    }
};
