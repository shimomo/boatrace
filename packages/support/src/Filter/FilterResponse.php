<?php

declare(strict_types=1);

namespace Boatrace\Support\Filter;

use Boatrace\Types\Contracts\Filter\FilterResponse as FilterResponseContract;

/**
 * @author shimomo
 */
final class FilterResponse implements FilterResponseContract
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
