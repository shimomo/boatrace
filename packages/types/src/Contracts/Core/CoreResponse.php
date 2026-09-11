<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Core;

/**
 * @author shimomo
 */
interface CoreResponse extends Core
{
    /**
     * @return ?string
     */
    public function getValue(): ?string;
}
