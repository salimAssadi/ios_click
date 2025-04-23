<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('departments')){
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->boolean('status')->default(true);
                $table->foreignId('parent_id')->nullable()->constrained('departments')->onDelete('cascade');
                $table->text('description')->nullable();
                $table->integer('level')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if(Schema::hasTable('departments')){
            Schema::dropIfExists('departments');
        }
    }
}
