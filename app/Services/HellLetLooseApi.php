<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use JsonException;

final class HellLetLooseApi
{
    public function getPlayerProfile(string $playerId): array
    {
        $response = Http::baseUrl(config('services.hll.url'))
            ->withToken(config('services.hll.token'))
            ->acceptJson()
            ->timeout(10)
            ->get('/get_player_profile', [
                'player_id' => $playerId,
            ])
            ->throw();

        try {
            $payload = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RequestException($response);
        }

        if (! is_array($payload)) {
            throw new RequestException($response);
        }

        return $payload;
    }
}
