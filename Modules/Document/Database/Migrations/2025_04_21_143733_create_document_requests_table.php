<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('document_requests')) {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('request_type_id')->constrained('request_types')->onDelete('restrict');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('status_id')->constrained('statuses')->default(1);
            $table->timestamp('action_at')->nullable(); // متى تم اعتماد أو رفض الطلب
            $table->foreignId('action_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('document_requests');
    }
}
