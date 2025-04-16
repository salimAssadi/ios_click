<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Get all tenant databases
        $tenantDbs = \DB::connection('crm')
            ->table('consulting_companies')
            ->pluck('name_en')
            ->map(function($name) {
                return 'iso_' . strtolower(str_replace(' ', '_', $name));
            });

        // Apply migration to each tenant database
        foreach ($tenantDbs as $database) {
            \DB::statement("USE `$database`");
            
            // Create migrations table if it doesn't exist
            if (!Schema::hasTable('migrations')) {
                Schema::create('migrations', function (Blueprint $table) {
                    $table->id();
                    $table->string('migration');
                    $table->integer('batch');
                });
            }
            
            // Add columns to users table
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'session_id')) {
                    $table->string('session_id')->nullable()->after('remember_token');
                }
                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('session_id');
                }
                if (!Schema::hasColumn('users', 'last_login_ip')) {
                    $table->string('last_login_ip')->nullable()->after('last_login_at');
                }
            });
        }
    }

    public function down()
    {
        // Get all tenant databases
        $tenantDbs = \DB::connection('crm')
            ->table('consulting_companies')
            ->pluck('name_en')
            ->map(function($name) {
                return 'iso_' . strtolower(str_replace(' ', '_', $name));
            });

        // Revert migration in each tenant database
        foreach ($tenantDbs as $database) {
            \DB::statement("USE `$database`");
            
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn(['session_id', 'last_login_at', 'last_login_ip']);
                });
            }
        }
    }
};
