<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'iso_dic'; 

    public function up(): void
    {
        Schema::connection($this->connection)->table('iso_specification_items', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->nullable()->after('item_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->table('iso_specification_items', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
