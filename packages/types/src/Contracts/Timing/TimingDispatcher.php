<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Timing;

/**
 * @author shimomo
 */
interface TimingDispatcher extends Timing
{
    /**
     * @param list<string>|string|null $value
     * @return array<non-empty-string, array{dur: ?float, desc: ?string}>
     */
    public function parse(array|string|null $value): array;
}
