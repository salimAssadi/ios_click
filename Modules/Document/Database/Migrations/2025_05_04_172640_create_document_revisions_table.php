<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('document_revisions')) {
            Schema::create('document_revisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('version_id')->constrained('document_versions')->onDelete('cascade');
                $table->string('revision_number', 10);
                $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('reviewed_at');
                $table->text('changes_summary')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamps();
                $table->softDeletes();

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
        if (Schema::hasTable('document_revisions')) {
            Schema::dropIfExists('document_revisions');
        }
    }
}
