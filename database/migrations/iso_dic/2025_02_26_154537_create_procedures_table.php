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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('procedure_name_ar');
            $table->string('procedure_name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('template_path')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->unsignedInteger('form_id')->default(0);
            $table->boolean('status')->default(false);
            $table->longText('content')->nullable();
            $table->boolean('enable_upload_file')->default(false);
            $table->boolean('enable_editor')->default(false);
            $table->boolean('has_menual_config')->default(false);
            $table->text('blade_view')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
