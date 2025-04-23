<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('departments')) {
            
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
            
        }
        if (!Schema::hasTable('units')) {
            
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained()->cascadeOnDelete();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('job_titles')) {
            
            Schema::create('job_titles', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->boolean('status')->default(true);
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
        Schema::dropIfExists('departments');
        Schema::dropIfExists('units');
        Schema::dropIfExists('job_titles');
    }
}
