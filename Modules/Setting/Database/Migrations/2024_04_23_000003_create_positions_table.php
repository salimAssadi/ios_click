<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('positions')){
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('reports_to_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        if(Schema::hasTable('positions')){
            Schema::dropIfExists('positions');
        }
    }
}
