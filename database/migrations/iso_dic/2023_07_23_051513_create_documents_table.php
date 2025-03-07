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
        Schema::connection($this->connection)->create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('category_id')->default(0);
            $table->integer('sub_category_id')->nullable()->default(0);
            $table->string('reference_id')->nullable();
            $table->integer('iso_system_id')->nullable();
            $table->text('description')->nullable();    
            $table->string('tages')->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('parent_id')->default(0);
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
        Schema::connection($this->connection)->dropIfExists('documents');
    }
};
