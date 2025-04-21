<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentHistoryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('document_history_log')) {
        Schema::create('document_history_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id'); 
            $table->unsignedBigInteger('version_id'); 
            $table->string('action_type');  
            $table->unsignedBigInteger('performed_by');  
            $table->text('notes')->nullable();  
            $table->text('change_summary')->nullable();  
            $table->timestamps();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('cascade');
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
        if (Schema::hasTable('document_history_log')) {
            Schema::dropIfExists('document_history_log');
        }
    }
}
