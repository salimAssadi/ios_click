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
        Schema::connection('tenant')->create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // policy, procedure, form, record
            $table->string('version');
            $table->string('status'); // draft, under_review, approved, archived
            $table->text('content');
            $table->foreignId('category_id')->constrained('document_categories');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approval_date')->nullable();
            $table->timestamp('review_date')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('documents');
    }
}
