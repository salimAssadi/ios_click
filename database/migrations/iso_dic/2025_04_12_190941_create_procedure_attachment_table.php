<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'iso_dic';
    public function up(): void
    {
        Schema::connection('iso_dic')->create('procedure_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedure_id')->constrained('procedures')->onDelete('cascade');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->timestamps();
        });

        Schema::connection('iso_dic')->create('sample_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained('samples')->onDelete('cascade');
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
        Schema::dropIfExists('procedure_attachment');
    }
};
