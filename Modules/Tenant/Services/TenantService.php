<?php

namespace Modules\Tenant\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Traits\TenantFileManager;

class TenantService
{
    use TenantFileManager;

    /**
     * Set up a new database connection for a tenant
     *
     * @param string $companyName
     * @return array|null Returns connection details if successful, null if company not found
     */
    public function setUpTenantConnection(string $companyName)
    {
        try {
            // First check if the company exists in CRM database
            $company = DB::connection('crm')
                ->table('consulting_companies')
                ->where(function($query) use ($companyName) {
                    $query->where('name_en', $companyName)
                          ->orWhere('name_ar', $companyName);
                })->first();
                

            if (!$company) {
                return null;
            }

            // The database name is the same as company name (you might want to use a different naming convention)
            $databaseName = 'iso_' . strtolower(str_replace(' ', '_', $company->name_en));

            // Set up the new connection configuration
            $config = [
                'driver' => 'mysql',
                'host' => "127.0.0.1",
                'port' =>   '3306',
                'database' => $databaseName,
                'username' => 'root',
                'password' => '12345678',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];

            // Add the new connection to the configuration
            Config::set("database.connections.tenant", $config);

            // Clear the previous connection
            DB::purge('tenant');

            // Test the connection
            DB::connection('tenant')->getPdo();

            return [
                'connection' => 'tenant',
                'database' => $databaseName,
                'company' => $company
            ];
        } catch (\Exception $e) {
            \Log::error('Tenant connection error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new database for a tenant
     *
     * @param int $companyId
     * @return bool
     */
    public function createTenantDatabase(int $companyId)
    {
        try {
            $databaseName = 'iso_'. $companyId;
            DB::statement("CREATE DATABASE IF NOT EXISTS {$databaseName}");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            
            // Create tenant record
            $tenant = \Modules\Tenant\Models\Tenant::create([
                'name' => $data['name'],
                'domain' => $data['domain'],
                'database' => $data['database']
            ]);

            // Create tenant storage directories
            $this->createTenantStorage($tenant->id);

            DB::commit();
            return $tenant;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
