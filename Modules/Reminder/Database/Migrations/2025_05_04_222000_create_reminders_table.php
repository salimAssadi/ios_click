<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('reminders')) {
            Schema::create('reminders', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->dateTime('remind_at');
                $table->string('reminder_type'); // document_expiry, personal, task, etc.
                $table->boolean('is_recurring')->default(false);
                $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly, custom
                $table->integer('recurrence_interval')->nullable(); // interval between recurrences
                $table->dateTime('recurrence_end_date')->nullable();
                $table->string('status')->default('pending'); // pending, sent, cancelled
                $table->dateTime('last_sent_at')->nullable();
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->json('recipients')->nullable(); // array of user IDs to receive this reminder
                $table->string('notification_channels')->default('email'); // email, sms, push, system, etc.
                $table->boolean('is_active')->default(true);

                // Polymorphic relationship fields - allows reminders to be attached to any model
                $table->string('remindable_type')->nullable();
                $table->unsignedBigInteger('remindable_id')->nullable();
                $table->index(['remindable_type', 'remindable_id']);

                // Custom fields
                $table->json('metadata')->nullable(); // for storing additional data related to the reminder

                // Audit fields
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
        if (Schema::hasTable('reminders')) {
            Schema::dropIfExists('reminders');
        }
    }
}
