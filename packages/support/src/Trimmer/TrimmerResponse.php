<?php

declare(strict_types=1);

namespace Boatrace\Support\Trimmer;

use Boatrace\Types\Contracts\Trimmer\TrimmerResponse as TrimmerResponseContract;

/**
 * @author shimomo
 */
final class TrimmerResponse implements TrimmerResponseContract
{
    /**
     * @param ?string $value
     */
    public function __construct(private readonly ?string $value = null)
    {
        //
    }

    /**
     * @return ?string
     */
    #[\Override]
    public function getValue(): ?string
    {
        return $this->value;
    }
}
