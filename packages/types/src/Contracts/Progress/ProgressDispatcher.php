<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Progress;

/**
 * @author shimomo
 */
interface ProgressDispatcher extends Progress
{
    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     * @return void
     */
    public function setEnabled(bool $enabled): void;

    /**
     * @param int<0, max> $totalSteps
     * @param ?string $message
     * @return void
     */
    public function start(int $totalSteps, ?string $message = null): void;

    /**
     * @param int<1, max> $step
     * @return void
     */
    public function advance(int $step = 1): void;

    /**
     * @param ?string $message
     * @return void
     */
    public function finish(?string $message = null): void;
}
