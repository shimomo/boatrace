<?php

declare(strict_types=1);

namespace Boatrace\Support\Validator;

use Boatrace\Types\Contracts\Validator\ValidatorResponse as ValidatorResponseContract;

/**
 * @author shimomo
 */
final class ValidatorResponse implements ValidatorResponseContract
{
    /**
     * @param ?int $value
     */
    public function __construct(private readonly ?int $value = null)
    {
        //
    }

    /**
     * @return ?int
     */
    #[\Override]
    public function getValue(): ?int
    {
        return $this->value;
    }
}
