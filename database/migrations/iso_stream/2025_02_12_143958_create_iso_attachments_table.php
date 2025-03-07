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
        Schema::create('iso_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iso_system_id'); // العلاقة مع جدول أنظمة الأيزو
            $table->string('name_ar'); 
            $table->string('name_en'); 
            $table->string('type'); 
            $table->string('file_path');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iso_attachments');
    }
};
