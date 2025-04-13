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
        // Create iso_policies table
        Schema::connection('iso_dic')->create('iso_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
                $table->string('name_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->longText('content')->nullable();
                $table->string('version')->nullable();
                $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
                $table->datetime('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
    
            // Create iso_policy_attachments table
            Schema::connection('iso_dic')->create('iso_policy_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('policy_id')->constrained('iso_policies')->onDelete('cascade');
                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime_type');
                $table->integer('file_size');
                $table->timestamps();
            });
    
            // Create iso_policy_archives table
            Schema::connection('iso_dic')->create('iso_policy_archives', function (Blueprint $table) {
                $table->id();
                $table->foreignId('policy_id')->constrained('iso_policies')->onDelete('cascade');
                $table->string('version')->nullable();
                $table->longText('content')->nullable();
                $table->string('name_ar')->nullable();
                $table->string('name_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('reason')->nullable();
                $table->datetime('archived_at')->useCurrent();
                $table->timestamps();
            });
    
            // Create iso_policy_versions table
            Schema::connection('iso_dic')->create('iso_policy_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('policy_id')->constrained('iso_policies')->onDelete('cascade');
                $table->string('version')->nullable();
                $table->longText('content')->nullable();
                $table->string('name_ar')->nullable();
                $table->string('name_en')->nullable();
                $table->text('description_ar')->nullable();
                $table->text('description_en')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->json('changes')->nullable(); // To store version changes as an array
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('iso_dic')->dropIfExists('iso_policy_versions');
        Schema::connection('iso_dic')->dropIfExists('iso_policy_archives');
        Schema::connection('iso_dic')->dropIfExists('iso_policy_attachments');
        Schema::connection('iso_dic')->dropIfExists('iso_policies');
    }
};
