<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    protected $connection = 'iso_dic'; 

    public function up()
    {
        Schema::connection($this->connection)->create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('value');
            $table->string('type')->nullable();
            $table->integer('parent_id');
            $table->timestamps();
            $table->unique(['name', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('settings');
    }
}
