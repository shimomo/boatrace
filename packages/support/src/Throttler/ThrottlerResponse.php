<?php

declare(strict_types=1);

namespace Boatrace\Support\Throttler;

use Boatrace\Types\Contracts\Throttler\ThrottlerResponse as ThrottlerResponseContract;

/**
 * @author shimomo
 */
final class ThrottlerResponse implements ThrottlerResponseContract
{
    /**
     * @param ?float $value
     */
    public function __construct(private readonly ?float $value = null)
    {
        //
    }

    /**
     * @return ?float
     */
    #[\Override]
    public function getValue(): ?float
    {
        return $this->value;
    }
}
