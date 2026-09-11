<?php

declare(strict_types=1);

namespace Boatrace\Support\Timing;

use Boatrace\Types\Contracts\Timing\TimingResponse as TimingResponseContract;

/**
 * @author shimomo
 */
final class TimingResponse implements TimingResponseContract
{
    /**
     * @param ?array<non-empty-string, array{dur: ?float, desc: ?string}> $value
     */
    public function __construct(private readonly ?array $value = null)
    {
        //
    }

    /**
     * @return ?array<non-empty-string, array{dur: ?float, desc: ?string}>
     */
    #[\Override]
    public function getValue(): ?array
    {
        return $this->value;
    }
}
