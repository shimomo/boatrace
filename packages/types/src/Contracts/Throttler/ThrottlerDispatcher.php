<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Throttler;

/**
 * @author shimomo
 */
interface ThrottlerDispatcher extends Throttler
{
    /**
     * @return float
     */
    public function getMinCallIntervalSeconds(): float;

    /**
     * @param float $seconds
     * @return void
     * @throws \ValueError
     */
    public function setMinCallIntervalSeconds(float $seconds): void;

    /**
     * @return void
     */
    public function throttle(): void;
}
