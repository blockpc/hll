<?php

declare(strict_types=1);

namespace Blockpc\App\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SqlWatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sql:watch {url? : Patron de URL, ej sistema/notas/*} {--off}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activa/desactiva log de SQL lento por patron de URL';

    public function handle(): int
    {
        if (! app()->isLocal()) {
            $this->warn('No se puede ejecutar en este entorno. Disponible solo en local.');

            return self::FAILURE;
        }

        if ($this->option('off')) {
            Cache::forget('sql_watch_url_pattern');
            $this->info('SQL watch desactivado.');

            return self::SUCCESS;
        }

        /** @var string|null $pattern */
        $pattern = $this->argument('url');

        if (! $pattern) {
            $current = Cache::get('sql_watch_url_pattern');
            $this->line('Patron actual: '.($current ?? 'ninguno'));

            return self::SUCCESS;
        }

        Cache::forever('sql_watch_url_pattern', ltrim($pattern, '/'));
        $this->info("SQL watch activo para: {$pattern}");

        return self::SUCCESS;
    }
}
