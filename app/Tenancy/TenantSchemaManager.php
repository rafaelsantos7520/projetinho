<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;

class TenantSchemaManager
{
    public function setSearchPath(string $schema, bool $reconnect = false): void
    {
        $connectionName = (string) config('tenancy.tenant_connection', config('database.default'));

        if ($reconnect) {
            config(["database.connections.$connectionName.database" => $schema]);
            DB::purge($connectionName);
            DB::reconnect($connectionName);
        }

        $connection = DB::connection($connectionName);
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $schema)) {
                abort(400, 'Invalid tenant schema.');
            }

            $schema = '"' . $schema . '"';
            $connection->statement(sprintf('SET search_path TO %s, public', $schema));

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            if (! preg_match('/^[a-zA-Z0-9_]+$/', $schema)) {
                abort(400, 'Invalid tenant database.');
            }

            $escaped = str_replace('`', '``', $schema);
            $connection->statement('USE `' . $escaped . '`');
            if (method_exists($connection, 'setDatabaseName')) {
                $connection->setDatabaseName($schema);
            }

            return;
        }

        abort(500, 'Unsupported database driver for tenancy.');
    }

    public function resetToPublic(): void
    {
        $connectionName = (string) config('tenancy.tenant_connection', config('database.default'));
        $connection = DB::connection($connectionName);
        $driver = $connection->getDriverName();

        if ($driver === 'pgsql') {
            $connection->statement('SET search_path TO public');

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $database = (string) config('tenancy.fallback_database');
            $this->setSearchPath($database);

            return;
        }

        if ($driver === 'sqlite') {
            return;
        }

        abort(500, 'Unsupported database driver for tenancy.');
    }
}
