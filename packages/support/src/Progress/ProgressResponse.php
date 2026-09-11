<?php

declare(strict_types=1);

namespace Boatrace\Support\Progress;

use Boatrace\Types\Contracts\Progress\ProgressResponse as ProgressResponseContract;

/**
 * @author shimomo
 */
final class ProgressResponse implements ProgressResponseContract
{
    /**
     * @param ?bool $value
     */
    public function __construct(private readonly ?bool $value = null)
    {
        //
    }

    /**
     * @return ?bool
     */
    #[\Override]
    public function getValue(): ?bool
    {
        return $this->value;
    }
}
