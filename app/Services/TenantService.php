<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantService
{
    /**
     * Set up a new database connection for a tenant
     *
     * @param string $companyName
     * @param string $email
     * @return array|null Returns connection details if successful, null if company not found
     */
    public function setUpTenantConnection(string $companyName, string $email)
    {
        // First check if the company exists in CRM database
        $company = DB::connection('crm')
            ->table('consulting_companies')
            ->where(function($query) use ($companyName) {
                $query->where('name_en', $companyName)
                      ->orWhere('name_ar', $companyName);
            })
            ->where('email', $email)
            ->first();

        if (!$company) {
            return null;
        }

        // The database name is the same as company name (you might want to use a different naming convention)
        $databaseName = 'iso_stream_' . $company->id;

        // Set up the new connection configuration
        $config = [
            'driver' => 'mysql',
            'host' => env('DB_ISO_STREAM_HOST', '127.0.0.1'),
            'port' => env('DB_ISO_STREAM_PORT', '3306'),
            'database' => $databaseName,
            'username' => env('DB_ISO_STREAM_USERNAME'),
            'password' => env('DB_ISO_STREAM_PASSWORD'),
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

        return [
            'connection' => 'tenant',
            'database' => $databaseName,
            'company' => $company
        ];
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
            $databaseName = 'iso_stream_' . $companyId;
            DB::statement("CREATE DATABASE IF NOT EXISTS {$databaseName}");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
