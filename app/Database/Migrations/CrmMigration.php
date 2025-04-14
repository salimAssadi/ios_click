<?php

namespace App\Database\Migrations;

use Illuminate\Database\Migrations\Migration;

abstract class CrmMigration extends Migration
{
    /**
     * Get the connection that should be used by the migration.
     *
     * @return string
     */
    public function getConnection()
    {
        return 'crm';
    }
}
