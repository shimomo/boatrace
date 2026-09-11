<?php

declare(strict_types=1);

namespace Boatrace\Core;

use Boatrace\Types\Contracts\Core\CoreResponse as CoreResponseContract;

/**
 * @author shimomo
 */
final class CoreResponse implements CoreResponseContract
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
