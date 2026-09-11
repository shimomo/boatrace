<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Definitions;

/**
 * @author shimomo
 */
interface Definitions
{
    /**
     * @return non-empty-string
     */
    public static function definitions(): string;
}
