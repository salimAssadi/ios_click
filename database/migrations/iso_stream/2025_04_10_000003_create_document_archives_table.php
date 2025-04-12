<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'iso_dic';

    public function up()
    {
        Schema::connection($this->connection)->create('document_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('archive_reason');
            $table->integer('archived_by');
            $table->timestamp('archived_at');
            $table->json('document_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('document_archives');
    }
};
