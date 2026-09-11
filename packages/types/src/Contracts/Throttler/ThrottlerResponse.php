<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Throttler;

/**
 * @author shimomo
 */
interface ThrottlerResponse extends Throttler
{
    /**
     * @return ?float
     */
    public function getValue(): ?float;
}
