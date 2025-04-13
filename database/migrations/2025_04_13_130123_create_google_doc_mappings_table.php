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
        Schema::create('google_doc_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('google_doc_id')->unique();
            $table->string('google_doc_url');
            $table->timestamp('last_synced_at')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->default('pending');
            $table->text('sync_error')->nullable();
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['google_doc_id']);
            $table->index(['sync_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_doc_mappings');
    }
};
