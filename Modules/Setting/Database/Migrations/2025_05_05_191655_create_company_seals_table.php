<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanySealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if (!Schema::hasTable('company_seals')) {
        Schema::create('company_seals', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');  
            $table->string('name_en');  
            $table->string('type'); 
            $table->string('file_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('company_seals')) {
            Schema::dropIfExists('company_seals');
        }
    }
}
