<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Drop the existing related_process column if it exists
            if (Schema::hasColumn('documents', 'related_process')) {
                $table->dropColumn('related_process');
            }
            
            // Add polymorphic relationship columns
            $table->string('documentable_type')->nullable()->after('document_type');
            $table->unsignedBigInteger('documentable_id')->nullable()->after('documentable_type');
            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remove the polymorphic relationship columns
            $table->dropIndex(['documentable_type', 'documentable_id']);
            $table->dropColumn(['documentable_type', 'documentable_id']);
            
            // Add back the original column
            $table->string('related_process')->nullable()->after('document_type');
        });
    }
};
