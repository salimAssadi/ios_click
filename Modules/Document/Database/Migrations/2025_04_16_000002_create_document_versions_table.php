<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('document_versions')) {
            Schema::create('document_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained()->onDelete('cascade');
                $table->string('version');
                $table->date('issue_date');
                $table->date('expiry_date');
                $table->date('review_due_date')->nullable();
                $table->foreignId('status_id')->constrained('statuses')->default(1);
                $table->date('approval_date')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->string('storage_path'); 
                $table->string('file_path'); 
                $table->text('change_notes')->nullable();
                $table->boolean('is_active')->default(false); 
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
        Schema::dropIfExists('document_versions');
    }
}
