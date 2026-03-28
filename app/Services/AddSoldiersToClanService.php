<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleSquadTypeEnum;
use App\Models\Clan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class AddSoldiersToClanService
{
    private ?Clan $clan = null;

    private ?string $bulkNames = null;

    public function for(Clan $clan): self
    {
        $this->clan = $clan;

        return $this;
    }

    public function names(string $bulkNames): self
    {
        $this->bulkNames = $bulkNames;

        return $this;
    }

    /**
     * @return array{
     *     created: int,
     *     skippedTooLong: list<string>,
     *     skippedDuplicates: list<string>,
     *     duplicatesIgnored: list<string>,
     *     skippedEmpty: int
     * }
     */
    public function saveSingle(string $name, ?RoleSquadTypeEnum $role = null, ?string $observation = null): array
    {
        $name = $this->normalizeName($name);
        $soldier = $this->clan->soldiers()->firstOrCreate(
            ['name' => $name],
            ['role' => $role, 'observation' => $observation]
        );

        if ($soldier->wasRecentlyCreated) {
            return [
                'created' => 1,
                'skippedTooLong' => [],
                'skippedDuplicates' => [],
                'duplicatesIgnored' => [],
                'skippedEmpty' => 0,
            ];
        }

        return [
            'created' => 0,
            'skippedTooLong' => [],
            'skippedDuplicates' => [],
            'duplicatesIgnored' => [$name],
            'skippedEmpty' => 0,
        ];
    }

    /**
     * @return array{
     *     created: int,
     *     skippedTooLong: list<string>,
     *     skippedDuplicates: list<string>,
     *     duplicatesIgnored: list<string>,
     *     skippedEmpty: int
     * }
     */
    public function saveBulk(): array
    {
        $skippedEmpty = 0;
        $skippedTooLong = [];
        $skippedDuplicates = [];
        $validNames = [];

        foreach (preg_split('/[\n,]+/', $this->bulkNames ?? '') as $raw) {
            $normalized = $this->normalizeName($raw);

            if ($normalized === '') {
                $skippedEmpty++;

                continue;
            }

            if (mb_strlen($normalized) > 32) {
                $skippedTooLong[] = $normalized;

                continue;
            }

            if (in_array($normalized, $validNames, true)) {
                $skippedDuplicates[] = $normalized;

                continue;
            }

            $validNames[] = $normalized;
        }

        $validNamesLower = array_map('mb_strtolower', $validNames);

        $existingNamesLower = [];

        if (! empty($validNamesLower)) {
            $existingNamesLower = $this->clan->soldiers()
                ->whereIn(DB::raw('LOWER(name)'), $validNamesLower)
                ->pluck(DB::raw('LOWER(name)'))
                ->toArray();
        }

        $newNames = array_values(array_filter($validNames, static fn (string $n) => ! in_array(mb_strtolower($n), $existingNamesLower, true)));
        $duplicatesIgnored = array_values(array_filter($validNames, static fn (string $n) => in_array(mb_strtolower($n), $existingNamesLower, true)));

        if (! empty($newNames)) {
            $timestamp = now();
            $clanId = $this->clan->id;

            $rows = array_map(static fn (string $name): array => [
                'clan_id' => $clanId,
                'name' => $name,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ], $newNames);

            $this->clan->soldiers()->insert($rows);
        }

        $created = count($newNames);

        return [
            'created' => $created,
            'skippedTooLong' => $skippedTooLong,
            'skippedDuplicates' => $skippedDuplicates,
            'duplicatesIgnored' => $duplicatesIgnored,
            'skippedEmpty' => $skippedEmpty,
        ];
    }

    /**
     * @param array{
     *     created: int,
     *     skippedTooLong: list<string>,
     *     skippedDuplicates: list<string>,
     *     duplicatesIgnored: list<string>,
     *     skippedEmpty: int
     * } $result
     */
    public function messages(array $result): string
    {
        $message = __('hll.clans.soldiers.create.bulk_message_success', ['count' => $result['created']]);

        if (! empty($result['skippedTooLong'])) {
            $message .= ' '.__('hll.clans.soldiers.create.bulk_skipped_too_long', [
                'count' => count($result['skippedTooLong']),
                'names' => implode(', ', $result['skippedTooLong']),
            ]);
        }

        if (! empty($result['skippedDuplicates'])) {
            $message .= ' '.__('hll.clans.soldiers.create.bulk_skipped_duplicates', [
                'count' => count($result['skippedDuplicates']),
                'names' => implode(', ', $result['skippedDuplicates']),
            ]);
        }

        if (! empty($result['duplicatesIgnored'])) {
            $message .= ' '.__('hll.clans.soldiers.create.bulk_duplicates_ignored', [
                'count' => count($result['duplicatesIgnored']),
                'names' => implode(', ', $result['duplicatesIgnored']),
            ]);
        }

        if ($result['skippedEmpty'] > 0) {
            $message .= ' '.__('hll.clans.soldiers.create.bulk_skipped_empty', [
                'count' => $result['skippedEmpty'],
            ]);
        }

        return $message;
    }

    private function normalizeName(string $name): string
    {
        return Str::transliterate(trim($name));
    }
}
