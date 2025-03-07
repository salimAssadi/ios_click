<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('procedure_template', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('content')->nullable();
            $table->integer('procedure_id')->nullable()->default(0);
            $table->integer('parent_id')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_template');
    }
};
