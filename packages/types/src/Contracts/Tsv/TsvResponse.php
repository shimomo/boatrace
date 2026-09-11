<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Tsv;

/**
 * @author shimomo
 */
interface TsvResponse extends Tsv
{
    /**
     * @return ?list<list<string>>
     */
    public function getValue(): ?array;
}
