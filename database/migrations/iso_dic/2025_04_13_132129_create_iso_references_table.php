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
        Schema::connection('iso_dic')->create('iso_references', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable(); 
            $table->string('name_en')->nullable(); 
            $table->boolean('is_published')->default(false); 
            $table->timestamps(); 
        });

        Schema::connection('iso_dic')->create('iso_reference_systems', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('reference_id')->constrained('iso_references')->onDelete('cascade'); 
            $table->foreignId('iso_system_id')->constrained('iso_systems')->onDelete('cascade'); 
            $table->timestamps(); 
            $table->unique(['reference_id', 'iso_system_id']);
        });

        Schema::connection('iso_dic')->create('iso_reference_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reference_id')->constrained('iso_references')->onDelete('cascade'); 
            $table->string('file_path'); 
            $table->string('original_name'); 
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
        Schema::connection('iso_dic')->dropIfExists('iso_reference_attachments');
        Schema::connection('iso_dic')->dropIfExists('iso_reference_systems');
        Schema::connection('iso_dic')->dropIfExists('iso_references');
    }
};
