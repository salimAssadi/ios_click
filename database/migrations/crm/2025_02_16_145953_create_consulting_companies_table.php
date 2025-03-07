<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consulting_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('logo')->nullable(); 
            $table->string('mobile')->nullable();
            $table->string('email')->unique();
            $table->string('stamp')->nullable(); 
            $table->string('signiture')->nullable(); 
            $table->boolean('is_default')->default(false);
            $table->string('contact_person')->nullable();
            $table->integer('client_count')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consulting_companies');
    }
};
