<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'iso_dic';

    public function up()
    {
        Schema::connection($this->connection)->create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->string('version_number');
            $table->text('changes_description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('google_doc_id')->nullable();
            $table->integer('created_by');
            $table->enum('status', ['draft', 'under_review', 'approved', 'rejected']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->dropIfExists('document_versions');
    }
};
