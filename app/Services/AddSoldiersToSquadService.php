<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Roster;
use App\Models\Squad;
use App\Models\SquadSoldier;
use Illuminate\Support\Str;

final class AddSoldiersToSquadService
{
    private ?Squad $squad = null;

    private ?Roster $roster = null;

    private ?string $bulkNames = null;

    private ?int $nextSlotNumber = null;

    public function for(Squad $squad): self
    {
        $this->squad = $squad;
        $this->roster = $squad->roster;

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
     *     skippedEmpty: int,
     *     skippedSquadFull: list<string>
     *     skippedRosterFull: list<string>
     * }
     */
    public function saveSingle(string $name): array
    {
        if ($this->squad === null || $this->roster === null) {
            throw new \LogicException('Call for() before saveBulk()');
        }

        $name = $this->normalizeName($name);

        $result = [
            'created' => 0,
            'skippedTooLong' => [],
            'skippedDuplicates' => [],
            'duplicatesIgnored' => [],
            'skippedEmpty' => 0,
            'skippedSquadFull' => [],
            'skippedRosterFull' => [],
        ];

        if ($name === '') {
            $result['skippedEmpty'] = 1;

            return $result;
        }

        if (mb_strlen($name) > 32) {
            $result['skippedTooLong'][] = $name;

            return $result;
        }

        $squadAvailable = $this->squad->capacity - $this->squad->soldiers()->count();
        $rosterAvailable = $this->roster->max_soldiers - $this->roster->assignedSoldiersCount();

        if ($rosterAvailable <= 0) {
            $result['skippedRosterFull'][] = $name;

            return $result;
        }

        if ($squadAvailable <= 0) {
            $result['skippedSquadFull'][] = $name;

            return $result;
        }

        $soldier = $this->addSoldierToSquad($name);

        if ($soldier->wasRecentlyCreated) {
            $result['created'] = 1;

            return $result;
        }

        $result['duplicatesIgnored'][] = $name;

        return $result;
    }

    /**
     * @return array{
     *     created: int,
     *     skippedTooLong: list<string>,
     *     skippedDuplicates: list<string>,
     *     duplicatesIgnored: list<string>,
     *     skippedEmpty: int,
     *     skippedSquadFull: list<string>
     *     skippedRosterFull: list<string>
     * }
     */
    public function saveBulk(): array
    {
        if ($this->squad === null || $this->roster === null) {
            throw new \LogicException('Call for() before saveSingle()');
        }

        $skippedEmpty = 0;
        $skippedTooLong = [];
        $skippedDuplicates = [];
        $validNames = [];

        $parts = preg_split('/[\n,]+/', $this->bulkNames ?? '') ?: [];

        foreach ($parts as $raw) {
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

        $created = 0;
        $duplicatesIgnored = [];
        $skippedSquadFull = [];
        $skippedRosterFull = [];

        $squadAvailable = $this->squad->capacity - $this->squad->soldiers()->count();
        $rosterAvailable = $this->roster->max_soldiers - $this->roster->assignedSoldiersCount();

        foreach ($validNames as $name) {
            if ($rosterAvailable <= 0) {
                $skippedRosterFull[] = $name;

                continue;
            }

            if ($squadAvailable <= 0) {
                $skippedSquadFull[] = $name;

                continue;
            }

            $soldier = $this->addSoldierToSquad($name);

            if ($soldier->wasRecentlyCreated) {
                $created++;
                $squadAvailable--;
                $rosterAvailable--;
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
            'skippedSquadFull' => $skippedSquadFull,
            'skippedRosterFull' => $skippedRosterFull,
        ];
    }

    /**
     * @param array{
     *     created: int,
     *     skippedTooLong: list<string>,
     *     skippedDuplicates: list<string>,
     *     duplicatesIgnored: list<string>,
     *     skippedEmpty: int,
     *     skippedFull: list<string>
     * } $result
     */
    public function messages(array $result): string
    {
        $message = __('hll.squads.service_messages.bulk_message_success', ['count' => $result['created']]);

        if (! empty($result['skippedTooLong'])) {
            $message .= ' '.__('hll.squads.service_messages.bulk_skipped_too_long', [
                'count' => count($result['skippedTooLong']),
                'names' => implode(', ', $result['skippedTooLong']),
            ]);
        }

        if (! empty($result['skippedDuplicates'])) {
            $message .= ' '.__('hll.squads.service_messages.bulk_skipped_duplicates', [
                'count' => count($result['skippedDuplicates']),
                'names' => implode(', ', $result['skippedDuplicates']),
            ]);
        }

        if (! empty($result['duplicatesIgnored'])) {
            $message .= ' '.__('hll.squads.service_messages.bulk_duplicates_ignored', [
                'count' => count($result['duplicatesIgnored']),
                'names' => implode(', ', $result['duplicatesIgnored']),
            ]);
        }

        if ($result['skippedEmpty'] > 0) {
            $message .= ' '.__('hll.squads.service_messages.bulk_skipped_empty', [
                'count' => $result['skippedEmpty'],
            ]);
        }

        if (! empty($result['skippedFull'])) {
            $message .= ' '.__('hll.squads.service_messages.bulk_skipped_full', [
                'count' => count($result['skippedFull']),
                'names' => implode(', ', $result['skippedFull']),
            ]);
        }

        return $message;
    }

    private function addSoldierToSquad(string $name): SquadSoldier
    {
        if ($this->nextSlotNumber === null) {
            $this->nextSlotNumber = ($this->squad->soldiers()->max('slot_number') ?? 0) + 1;
        }

        $existing = $this->roster->squadSoldiers()
            ->whereRaw('LOWER(display_name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $nextSlot = $this->nextSlotNumber;
        $this->nextSlotNumber++;

        $soldier = $this->squad->soldiers()->create([
            'display_name' => $name,
            'slot_number' => $nextSlot,
        ]);

        return $soldier;
    }

    private function normalizeName(string $name): string
    {
        return Str::transliterate(trim($name));
    }
}
