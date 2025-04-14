<?php

use App\Database\Migrations\CrmMigration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends CrmMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logged_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('ip')->nullable();
            $table->dateTime('date')->nullable();
            $table->text('details')->nullable();
            $table->string('type')->nullable();
            $table->integer('parent_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logged_histories');
    }
};


