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
        Schema::connection($this->connection)->create('iso_specification_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iso_system_id'); 
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('item_number'); 
            $table->text('inspection_question')->nullable(); 
            $table->text('sub_inspection_question')->nullable(); 
            $table->text('additional_text')->nullable(); 
            $table->string('attachment')->nullable(); 
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('iso_specification_items')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('iso_specification_items');
    }
};
