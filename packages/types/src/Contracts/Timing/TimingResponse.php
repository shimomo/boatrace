<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Timing;

/**
 * @author shimomo
 */
interface TimingResponse extends Timing
{
    /**
     * @return ?array<non-empty-string, array{dur: ?float, desc: ?string}>
     */
    public function getValue(): ?array;
}
