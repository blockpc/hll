<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

final class HellLetLooseApi
{
    public function getPlayerProfile(string $playerId): array
    {
        return Http::baseUrl(config('services.hll.url'))
            ->withToken(config('services.hll.token'))
            ->acceptJson()
            ->timeout(10)
            ->get('/get_player_profile', [
                'player_id' => $playerId,
            ])
            ->throw()
            ->json();
    }
}
