<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('documents')) {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('document_number')->unique();
            $table->string('document_type'); // policy, procedure, work_instruction, form, manual
            $table->string('department')->nullable(); // quality, operations, hr, finance, it
            $table->text('description')->nullable();
            $table->string('related_process')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->date('creation_date');
            $table->boolean('is_obsolete')->default(false);
            $table->date('obsoleted_date')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('documents');
    }
}
