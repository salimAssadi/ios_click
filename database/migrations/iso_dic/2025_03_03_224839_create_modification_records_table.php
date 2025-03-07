<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('modification_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->string('version_number')->nullable(); // رقم الإصدار
            $table->date('issue_date')->nullable(); // تاريخ الإصدار
            $table->text('modification_description')->nullable(); // وصف التعديل
            $table->string('modified_by')->nullable(); // القائم بالتعديل
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modification_records');
    }
};
