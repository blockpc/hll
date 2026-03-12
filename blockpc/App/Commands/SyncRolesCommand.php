<?php

declare(strict_types=1);

namespace Blockpc\App\Commands;

use Blockpc\App\Services\RoleSynchronizerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class SyncRolesCommand extends Command
{
    protected $signature = 'blockpc:roles
                            {--check : Solo verificar roles existentes}
                            {--orphans : Mostrar roles huérfanos}
                            {--prune : Eliminar roles huérfanos}
                            {--ci : Modo continuo para CI/CD}';

    protected $description = 'Sincroniza, valida y limpia los roles definidos en el sistema';

    public function handle(RoleSynchronizerService $synchronizer): int
    {
        $errors = 0;
        $check = (bool) $this->option('check');
        $orphans = (bool) $this->option('orphans');
        $prune = (bool) $this->option('prune');

        $selectedActions = array_filter([$check, $orphans, $prune]);

        if (count($selectedActions) > 1) {
            $this->error('Las opciones --check, --orphans y --prune son mutuamente excluyentes. Usa solo una.');

            return 1;
        }

        if ($check) {
            $errors = $this->handleCheck($synchronizer);
        } elseif ($orphans) {
            $errors = $this->handleOrphans($synchronizer);
        } elseif ($prune) {
            $errors = $this->handlePrune($synchronizer);
        } else {
            $errors = $this->handleSync($synchronizer);
        }

        if ($errors > 0) {
            Log::error("Errores de sincronización de roles: {$errors}");
            $this->error("Errores de sincronización de roles: {$errors}");

            return 1;
        }

        return 0;
    }

    private function handleCheck(RoleSynchronizerService $synchronizer): int
    {
        $errors = 0;
        $missing = $synchronizer->getMissing();

        if ($missing->isEmpty()) {
            $this->info('✅ Todo sincronizado.');
        } else {
            $this->warn('⚠️  Roles faltantes:');
            foreach ($missing as $role) {
                $name = $role['name'];
                $guard = $role['guard_name'] ?? 'web';
                $this->warn("❌ Falta rol: {$name} (guard: {$guard})");
                $errors++;
            }
        }

        return $errors;
    }

    private function handleOrphans(RoleSynchronizerService $synchronizer): int
    {
        $orphans = $synchronizer->getOrphans();

        if ($orphans->isEmpty()) {
            $this->info('✅ No hay roles huérfanos.');

            return 0;
        }

        $this->warn('⚠️  Roles huérfanos:');
        foreach ($orphans as $orphan) {
            $this->line("- {$orphan->name} ({$orphan->guard_name})");
        }

        return $orphans->count();
    }

    private function handlePrune(RoleSynchronizerService $synchronizer): int
    {
        $orphans = $synchronizer->getOrphans();

        if ($orphans->isEmpty()) {
            $this->info('✅ No hay roles huérfanos.');

            return 0;
        }

        if (! $this->option('ci') && ! $this->confirm("¿Eliminar {$orphans->count()} roles huérfanos?", false)) {
            $this->info('🛑 Cancelado.');

            return 0;
        }

        $deleted = $synchronizer->prune();
        $this->info("🗑️ Eliminados: {$deleted} roles huérfanos.");

        return 0;
    }

    private function handleSync(RoleSynchronizerService $synchronizer): int
    {
        $synchronizer->sync();
        $this->info('🎉 Roles sincronizados.');

        return 0;
    }
}
