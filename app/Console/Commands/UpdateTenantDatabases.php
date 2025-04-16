<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;


class UpdateTenantDatabases extends Command
{
    protected $signature = 'tenant:update-databases';
    protected $description = 'Add session tracking columns to all tenant databases';

    public function handle()
    {
        $this->info('Starting tenant database updates...');

        try {
            // Get all tenant databases
            $tenantDbs = DB::connection('crm')
                ->table('consulting_companies')
                ->pluck('name_en')
                ->map(function($name) {
                    return 'iso_' . strtolower(str_replace(' ', '_', $name));
                });

                foreach ($tenantDbs as $database) {
                    $this->info("Processing database: {$database}");
                
                    try {
                        // Set the tenant database as the default connection
                        Config::set('database.connections.tenant.database', $database);
                        DB::purge('tenant'); // Clear the previous connection
                        DB::reconnect('tenant'); // Reconnect with the new database
                
                        // Use the tenant connection explicitly
                        Schema::connection('tenant')->table('users', function (Blueprint $table) {
                            if (!Schema::connection('tenant')->hasColumn('users', 'session_id')) {
                                $table->string('session_id')->nullable()->after('remember_token');
                                $this->info('Added session_id column');
                            }
                
                            if (!Schema::connection('tenant')->hasColumn('users', 'last_login_at')) {
                                $table->timestamp('last_login_at')->nullable()->after('session_id');
                                $this->info('Added last_login_at column');
                            }
                
                            if (!Schema::connection('tenant')->hasColumn('users', 'last_login_ip')) {
                                $table->string('last_login_ip')->nullable()->after('last_login_at');
                                $this->info('Added last_login_ip column');
                            }
                        });
                
                        $this->info("Successfully updated {$database}");
                    } catch (\Exception $e) {
                        $this->error("Error processing {$database}: " . $e->getMessage());
                    }
                }
            
            $this->info('All tenant databases have been updated successfully.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to update tenant databases: ' . $e->getMessage());
            return 1;
        }
    }
}
