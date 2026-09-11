<?php

declare(strict_types=1);

namespace Boatrace\Support\Json;

use Boatrace\Core\Concerns\RejectsUndefinedCalls;
use Boatrace\Types\Contracts\Json\JsonDispatcher as JsonDispatcherContract;
use JsonException;

/**
 * @author shimomo
 */
final class JsonDispatcher implements JsonDispatcherContract
{
    use RejectsUndefinedCalls;

    /**
     * @param ?string $value
     * @return ?array<array-key, mixed>
     */
    #[\Override]
    public function decode(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }
}
