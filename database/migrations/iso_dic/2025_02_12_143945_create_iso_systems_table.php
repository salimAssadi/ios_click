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
        Schema::connection($this->connection)->create('iso_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->string('name_en');
            $table->string('code')->unique(); 
            $table->text('specification')->nullable();
            $table->string('version')->nullable(); 
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('iso_systems');
    }
};
