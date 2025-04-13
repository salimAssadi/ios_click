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
        Schema::connection('iso_dic')->create('iso_instructions', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable(); // Arabic name (nullable)
            $table->string('name_en')->nullable(); // English name (nullable)
            $table->text('description_ar')->nullable(); // Arabic description (nullable)
            $table->text('description_en')->nullable(); // English description (nullable)
            $table->longText('content')->nullable(); // Content (nullable, can store large text)
            $table->boolean('is_published')->default(false); // Boolean flag for publication status
            $table->timestamps();
        });

        Schema::connection('iso_dic')->create('iso_instruction_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruction_id')->constrained('iso_instructions')->onDelete('cascade');
            $table->foreignId('procedure_id')->constrained('procedures')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::connection('iso_dic')->create('iso_instruction_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instruction_id')->constrained('iso_instructions')->onDelete('cascade');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('iso_dic')->dropIfExists('iso_instructions');
        Schema::connection('iso_dic')->dropIfExists('iso_instruction_procedures');
        Schema::connection('iso_dic')->dropIfExists('iso_instruction_attachments'); 
    }
};
