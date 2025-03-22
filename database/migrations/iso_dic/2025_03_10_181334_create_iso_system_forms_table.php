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
        Schema::create('iso_system_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('category_id')->default(0);
            $table->integer('iso_system_id')->default(0);
            $table->integer('procedure_id')->default(0);
            $table->integer('iso_system_procedure_id')->default(0);
            $table->integer('form_id')->default(0);
            $table->string('form_coding')->nullable();
            $table->text('description')->nullable();    
            $table->integer('created_by')->default(0);
            $table->integer('parent_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iso_system_forms');
    }
};
