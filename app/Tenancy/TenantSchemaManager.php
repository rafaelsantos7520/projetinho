<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;

class TenantSchemaManager
{
    public function setSearchPath(string $schema): void
    {
        if (! preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $schema)) {
            abort(400, 'Invalid tenant schema.');
        }

        $schema = '"'.$schema.'"';

        DB::connection()->statement(sprintf('SET search_path TO %s, public', $schema));
    }

    public function resetToPublic(): void
    {
        DB::connection()->statement('SET search_path TO public');
    }
}
