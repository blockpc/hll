<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleSquadTypeEnum;
use App\Models\Clan;
use App\Models\Soldier;
use App\Models\Squad;
use App\Models\SquadSoldier;
use Illuminate\Support\Str;

final class AddSoldiersService
{
    public bool $isOnlyOneName = false;

    public ?string $bulkNames = null;

    public ?Clan $clan = null;

    public ?Squad $squad = null;

    private ?int $nextSlotNumber = null;

    public function configure(bool $isOnlyOneName = false): self
    {
        $this->isOnlyOneName = $isOnlyOneName;

        return $this;
    }

    public function for(Clan|Squad $clanOrSquad): self
    {
        if ($clanOrSquad instanceof Clan) {
            $this->clan = $clanOrSquad;
        }

        if ($clanOrSquad instanceof Squad) {
            $this->squad = $clanOrSquad;
        }

        return $this;
    }

    public function names(string $bulkNames): self
    {
        $this->bulkNames = $bulkNames;

        return $this;
    }

    public function saveSingle(string $name, ?RoleSquadTypeEnum $role = null, ?string $observation = null): array
    {
        $name = $this->normalizeSoldierName($name);
        $soldier = $this->addSoldier($name, $role, $observation);
        if ($soldier->wasRecentlyCreated) {
            return [
                'created' => 1,
                'skippedTooLong' => [],
                'skippedDuplicates' => [],
                'duplicatesIgnored' => [],
                'skippedEmpty' => 0,
            ];
        } else {
            return [
                'created' => 0,
                'skippedTooLong' => [],
                'skippedDuplicates' => [],
                'duplicatesIgnored' => [$name],
                'skippedEmpty' => 0,
            ];
        }
    }

    public function saveBulk(): array
    {
        $skippedEmpty = 0;
        $skippedTooLong = [];
        $skippedDuplicates = [];
        $validNames = [];

        foreach (preg_split('/[\n,]+/', $this->bulkNames ?? '') as $raw) {
            $normalized = $this->normalizeSoldierName($raw);

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

        $created = 0;
        $duplicatesIgnored = [];

        foreach ($validNames as $name) {
            $soldier = $this->addSoldier($name);
            if ($soldier->wasRecentlyCreated) {
                $created++;
            } else {
                $duplicatesIgnored[] = $name;
            }
        }

        return [
            'created' => $created,
            'skippedTooLong' => $skippedTooLong,
            'skippedDuplicates' => $skippedDuplicates,
            'duplicatesIgnored' => $duplicatesIgnored,
            'skippedEmpty' => $skippedEmpty,
        ];
    }

    protected function normalizeSoldierName(string $name): string
    {
        return Str::transliterate(trim($name));
    }

    private function addSoldier(string $name, ?RoleSquadTypeEnum $role = null, ?string $observation = null): Soldier|SquadSoldier
    {
        if (isset($this->squad)) {
            return $this->addSoldierToSquad($name);
        }

        if (isset($this->clan)) {
            return $this->addSoldierToClan($name, $role, $observation);
        }

        throw new \LogicException('No squad or clan specified for adding soldiers.');
    }

    protected function addSoldierToClan(string $name, ?RoleSquadTypeEnum $role = null, ?string $observation = null): Soldier
    {
        return $this->clan->soldiers()->firstOrCreate(
            ['name' => $name],
            ['role' => $role, 'observation' => $observation]
        );
    }

    protected function addSoldierToSquad(string $name): SquadSoldier
    {
        if ($this->nextSlotNumber === null) {
            $this->nextSlotNumber = $this->squad->soldiers()->count() + 1;
        }

        $soldier = $this->squad->soldiers()->firstOrCreate(
            ['display_name' => $name],
            ['slot_number' => $this->nextSlotNumber]
        );

        if ($soldier->wasRecentlyCreated) {
            $this->nextSlotNumber++;
        }

        return $soldier;
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
}
