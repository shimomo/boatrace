<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Progress;

/**
 * @author shimomo
 */
interface ProgressResponse extends Progress
{
    /**
     * @return ?bool
     */
    public function getValue(): ?bool;
}
