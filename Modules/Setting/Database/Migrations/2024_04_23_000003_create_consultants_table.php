<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultantsTable extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('consultants')){
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('specialization');
            $table->text('expertise')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('consultants');
    }
}
