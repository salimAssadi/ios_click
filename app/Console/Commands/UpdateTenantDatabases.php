<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class UpdateTenantDatabases extends Command
{
    protected $signature = 'tenant:update-databases {--module= : Specific module to migrate}';
    protected $description = 'Update all tenant databases including module migrations';

    public function handle()
    {
        $this->info('Starting tenant database updates...');
        $specificModule = $this->option('module');

        try {
            // Get all tenant databases
            $tenantDbs = DB::connection('crm')
                ->table('consulting_companies')
                ->pluck('name_en')
                ->map(function($name) {
                    return 'isoclick_' . strtolower(str_replace(' ', '_', $name));
                });

            foreach ($tenantDbs as $database) {
                $this->info("Processing database: {$database}");
            
                try {
                    // Set the tenant database as the default connection
                    Config::set('database.connections.tenant.database', $database);
                    DB::purge('tenant'); // Clear the previous connection
                    DB::reconnect('tenant'); // Reconnect with the new database
            
                    // Run base migrations if needed
                    $this->runBaseMigrations();

                    // Run module migrations
                    if ($specificModule) {
                        $this->runModuleMigrations($specificModule);
                    } else {
                        $this->runAllModuleMigrations();
                    }
            
                    $this->info("Successfully updated {$database}");
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'already exists')) {
                        $this->warn("Table already exists in {$database}, continuing...");
                        continue;
                    }
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

    protected function runBaseMigrations()
    {
        try {
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
        } catch (\Exception $e) {
            if (!str_contains($e->getMessage(), 'already exists')) {
                throw $e;
            }
            $this->warn('Base tables already exist, skipping...');
        }
    }

    protected function runModuleMigrations($moduleName)
    {
        $module = Module::find($moduleName);
        if (!$module) {
            $this->error("Module {$moduleName} not found");
            return;
        }

        $this->info("Running migrations for module: {$moduleName}");
        try {
            // We'll use --force to run migrations even in production
            // and --seed to seed the database if seeds are available
            Artisan::call('module:migrate', [
                'module' => $moduleName,
                '--database' => 'tenant',
                '--force' => true
            ]);
            
            $output = Artisan::output();
            if (empty(trim($output))) {
                $this->info("No new migrations to run for {$moduleName}");
            } else {
                $this->info($output);
            }
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'already exists')) {
                $this->warn("Some tables for {$moduleName} already exist, continuing...");
                return;
            }
            throw $e;
        }
    }

    protected function runAllModuleMigrations()
    {
        foreach (Module::getEnabled() as $module) {
            try {
                $this->runModuleMigrations($module->getName());
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'already exists')) {
                    throw $e;
                }
                $this->warn("Some tables for {$module->getName()} already exist, continuing...");
            }
        }
    }
}
