<?php

declare(strict_types=1);

namespace Boatrace\Support\Tsv;

use Boatrace\Types\Contracts\Tsv\TsvResponse as TsvResponseContract;

/**
 * @author shimomo
 */
final class TsvResponse implements TsvResponseContract
{
    /**
     * @param ?list<list<string>> $value
     */
    public function __construct(private readonly ?array $value = null)
    {
        //
    }

    /**
     * @return ?list<list<string>>
     */
    #[\Override]
    public function getValue(): ?array
    {
        return $this->value;
    }
}
