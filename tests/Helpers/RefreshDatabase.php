<?php

namespace Tests\Helpers;

trait RefreshDatabase
{
    public function refreshDatabase()
    {
        $this->artisan('migrate');

        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            $database->connection($name)->beginTransaction();
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $connection = $database->connection($name);

                $connection->rollBack();
                $connection->disconnect();
            }

            $this->artisan('migrate:rollback');
        });
    }

    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact : [null];
    }
}
