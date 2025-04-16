<?php

namespace App\Traits;

trait TenantConnection
{
    /**
     * Get the connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return 'tenant';
    }

    /**
     * Set the connection name for the model.
     *
     * @param string $name
     * @return $this
     */
    public function setConnection($name)
    {
        $this->connection = $name;
        return $this;
    }
}
