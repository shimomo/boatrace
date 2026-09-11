<?php

declare(strict_types=1);

namespace Boatrace\Support\Json;

use Boatrace\Types\Contracts\Json\JsonResponse as JsonResponseContract;

/**
 * @author shimomo
 */
final class JsonResponse implements JsonResponseContract
{
    /**
     * @param ?array<array-key, mixed> $value
     */
    public function __construct(private readonly ?array $value = null)
    {
        //
    }

    /**
     * @return ?array<array-key, mixed>
     */
    #[\Override]
    public function getValue(): ?array
    {
        return $this->value;
    }
}
