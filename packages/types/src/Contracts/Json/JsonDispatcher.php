<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Json;

/**
 * @author shimomo
 */
interface JsonDispatcher extends Json
{
    /**
     * @param ?string $value
     * @return ?array<array-key, mixed>
     */
    public function decode(?string $value): ?array;
}
