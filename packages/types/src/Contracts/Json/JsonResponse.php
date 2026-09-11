<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Json;

/**
 * @author shimomo
 */
interface JsonResponse extends Json
{
    /**
     * @return ?array<array-key, mixed>
     */
    public function getValue(): ?array;
}
