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
    protected $connection = 'iso_dic'; 
    public function up()
    {
        Schema::connection($this->connection)->create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar'); 
            $table->string('name_en'); 
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
        Schema::connection($this->connection)->dropIfExists('unit_types');
    }
};
