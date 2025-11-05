<?php

namespace App\Providers;

use Illuminate\Database\Connectors\PostgresConnector;
use Illuminate\Support\ServiceProvider;

class NeonDatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Override PostgreSQL connector to add Neon endpoint support
        $this->app->bind('db.connector.pgsql', function ($app) {
            return new class extends PostgresConnector {
                protected function getDsn(array $config)
                {
                    // Start with the standard DSN
                    $dsn = parent::getDsn($config);
                    
                    // Add Neon endpoint option if host contains neon.tech
                    if (isset($config['host']) && str_contains($config['host'], 'neon.tech')) {
                        // Extract endpoint ID from hostname (e.g., ep-soft-fire-a1ukmipm from ep-soft-fire-a1ukmipm.ap-southeast-1.aws.neon.tech)
                        preg_match('/^(ep-[^.]+)/', $config['host'], $matches);
                        if (!empty($matches[1])) {
                            $endpoint = $matches[1];
                            // Append options parameter
                            $dsn .= ";options='endpoint=$endpoint'";
                        }
                    }
                    
                    return $dsn;
                }
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
