<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReminderRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('reminder_recipients')) {
            Schema::create('reminder_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reminder_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('reminder_id')
                  ->references('id')
                  ->on('reminders')
                  ->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            // Unique constraint to prevent duplicate recipients for the same reminder
            $table->unique(['reminder_id', 'user_id']);
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
        if (Schema::hasTable('reminder_recipients')) {
            Schema::dropIfExists('reminder_recipients');
        }
    }
}
