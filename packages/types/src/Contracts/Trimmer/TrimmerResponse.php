<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Trimmer;

/**
 * @author shimomo
 */
interface TrimmerResponse extends Trimmer
{
    /**
     * @return ?string
     */
    public function getValue(): ?string;
}
