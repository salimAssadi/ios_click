<?php

namespace Modules\Document\Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentRequestIdColumn extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('document_requests', 'parent_request_id')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->foreignId('parent_request_id')->nullable()->after('assigned_to')
                      ->constrained('document_requests')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('document_requests', 'parent_request_id')) {
            Schema::table('document_requests', function (Blueprint $table) {
                $table->dropForeign(['parent_request_id']);
                $table->dropColumn('parent_request_id');
            });
        }
    }
}
