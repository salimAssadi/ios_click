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
        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->string('name_en'); 
            $table->unsignedBigInteger('parent_unit_id')->nullable();
            $table->foreignId('unit_type_id'); 
            $table->foreignId('customer_id'); 
            $table->integer('num_employees')->nullable(); 
            $table->integer('num_operation')->nullable(); 
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
    }
};
