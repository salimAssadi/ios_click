<?php

namespace Database\Migrations\Crm;

use Illuminate\Database\Migrations\Migration;

abstract class CrmMigration extends Migration
{
    /**
     * Get the connection that should be used by the migration.
     *
     * @return string
     */
    protected function getConnection()
    {
        return 'crm';
    }
}

