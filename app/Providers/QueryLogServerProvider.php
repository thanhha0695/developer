<?php

namespace App\Providers;

use DB;
use Log;
use Event;
use DateTime;
use Carbon\Carbon;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class QueryBuilderServerProvider
 * @package App\Providers
 */
class QueryLogServerProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        if (config('logging.sqllog') === false) {
            return;
        }

        DB::listen(function ($query) {
            $sql = $query->sql;
            foreach ($query->bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'{$binding}'";
                } elseif ($binding === null) {
                    $binding = 'NULL';
                } elseif ($binding instanceof Carbon) {
                    $binding = "'{$binding->toDateTimeString()}'";
                } elseif ($binding instanceof DateTime) {
                    $binding = "'{$binding->format('Y-m-d H:i:s')}'";
                }

                $sql = preg_replace("/\?/", $binding, $sql, 1);
            }

            Log::channel('sqllog')->debug($query->connectionName, ['query' => $sql, 'time' => "$query->time ms"]);
        });

        Event::listen(TransactionBeginning::class, function (TransactionBeginning $event) {
            Log::channel('sqllog')->debug('START TRANSACTION');
        });

        Event::listen(TransactionCommitted::class, function (TransactionCommitted $event) {
            Log::channel('sqllog')->debug('COMMIT');
        });

        Event::listen(TransactionRolledBack::class, function (TransactionRolledBack $event) {
            Log::channel('sqllog')->debug('ROLLBACK');
        });
    }
}
