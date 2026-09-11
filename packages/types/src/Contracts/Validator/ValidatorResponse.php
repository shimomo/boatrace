<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Validator;

/**
 * @author shimomo
 */
interface ValidatorResponse extends Validator
{
    /**
     * @return ?int
     */
    public function getValue(): ?int;
}
