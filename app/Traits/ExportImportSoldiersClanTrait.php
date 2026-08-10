<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\RoleSquadTypeEnum;
use App\Models\Soldier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportImportSoldiersClanTrait
{
    use WithFileUploads;

    /** @var TemporaryUploadedFile|null */
    public $importFile;

    public function exportSoldiers(): StreamedResponse
    {
        $filename = $this->clan->slug.'-soldiers.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['name', 'role', 'observation']);

            $this->clan->soldiers()
                ->orderBy('name')
                ->select(['name', 'role', 'observation'])
                ->lazy(500)
                ->each(function (Soldier $soldier) use ($handle): void {
                    fputcsv($handle, [
                        $this->escapeCsvFormula($soldier->name),
                        $soldier->role?->value,
                        $soldier->observation !== null ? $this->escapeCsvFormula($soldier->observation) : null,
                    ]);
                });

            fclose($handle);
        }, $filename);
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|mimes:csv,txt|max:10240',
        ], [
            'importFile.required' => __('hll.clans.soldiers.import.validation.required'),
            'importFile.mimes' => __('hll.clans.soldiers.import.validation.mimes'),
            'importFile.max' => __('hll.clans.soldiers.import.validation.max'),
        ], [
            'importFile' => __('hll.clans.soldiers.import.validation.file'),
        ]);

        $handle = fopen($this->importFile->getRealPath(), 'r');

        if ($handle === false) {
            throw ValidationException::withMessages([
                'importFile' => __('hll.clans.soldiers.import.validation.required'),
            ]);
        }

        $expectedHeader = ['name', 'role', 'observation'];

        try {
            $soldiersCreatedsCount = DB::transaction(function () use ($handle, $expectedHeader): int {
                $header = fgetcsv($handle);

                if (! is_array($header)) {
                    throw ValidationException::withMessages([
                        'importFile' => 'El archivo CSV no tiene cabecera válida.',
                    ]);
                }

                $header = array_map(function (string $column): string {
                    $column = preg_replace('/^\xEF\xBB\xBF/', '', $column);

                    return trim($column);
                }, $header);

                if ($header !== $expectedHeader) {
                    throw ValidationException::withMessages([
                        'importFile' => 'La cabecera del CSV debe ser exactamente: name,role,observation.',
                    ]);
                }

                $soldiersCreatedsCount = 0;

                while (($row = fgetcsv($handle)) !== false) {
                    if (count(array_filter($row, static fn (?string $value): bool => trim((string) $value) !== '')) === 0) {
                        continue;
                    }

                    // Ignore comments
                    if (str_starts_with(trim($row[0] ?? ''), '#')) {
                        continue;
                    }

                    if (count($row) !== count($header)) {
                        throw ValidationException::withMessages([
                            'importFile' => 'Todas las filas deben tener la misma cantidad de columnas que la cabecera.',
                        ]);
                    }

                    $data = array_combine($header, $row);
                    $name = trim((string) ($data['name'] ?? ''));

                    if ($name === '') {
                        throw ValidationException::withMessages([
                            'importFile' => 'El nombre del soldado es obligatorio en cada fila.',
                        ]);
                    }

                    $roleValue = trim((string) ($data['role'] ?? ''));
                    $role = null;

                    if ($roleValue !== '') {
                        $role = RoleSquadTypeEnum::tryFrom($roleValue);

                        if ($role === null) {
                            throw ValidationException::withMessages([
                                'importFile' => 'El rol del soldado no es válido.',
                            ]);
                        }
                    }

                    $this->clan->soldiers()->updateOrCreate([
                        'name' => $name,
                    ], [
                        'role' => $role,
                        'observation' => $data['observation'] ?? null,
                    ]);

                    $soldiersCreatedsCount++;
                }

                return $soldiersCreatedsCount;
            });
        } finally {
            fclose($handle);
        }

        $this->reset('importFile');

        $this->alertSuccess(__('hll.clans.soldiers.import.message_success', ['count' => $soldiersCreatedsCount]), title: __('hll.clans.soldiers.import.title'));
    }

    public function resetImportFile(): void
    {
        $this->reset('importFile');
    }

    public function downloadImportTemplate(): StreamedResponse
    {
        $filename = 'soldiers-import-template.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['name', 'role', 'observation']);

            // Instructions
            fputcsv($handle, ['# The first line is the header and must be present in the file.']);
            fputcsv($handle, ['# The file can be .cvs or .txt file.']);
            fputcsv($handle, ['# Instructions']);
            fputcsv($handle, ['# Empty lines are ignored']);
            fputcsv($handle, ['# Lines starting with # are ignored']);
            fputcsv($handle, ['# The following line imports only the name']);

            fputcsv($handle, [
                'Name Soldier',
                '',
                '',
            ]);

            fputcsv($handle, ['# The following line imports name and role']);

            fputcsv($handle, [
                'Name Soldier',
                RoleSquadTypeEnum::RIFLEMAN->value,
                '',
            ]);

            // Examples
            fputcsv($handle, ['# Examples with all fields']);

            foreach (RoleSquadTypeEnum::cases() as $index => $role) {
                fputcsv($handle, [
                    'Name Soldier '.$role->value,
                    $role->value,
                    'This is an example Soldier with role '.$role->label().'.',
                ]);
            }

            fclose($handle);
        }, $filename);
    }

    private function escapeCsvFormula(string $value): string
    {
        if (preg_match('/^[=+\-@]/', $value) === 1) {
            return "'{$value}";
        }

        return $value;
    }
}
