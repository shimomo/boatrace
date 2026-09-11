<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Tsv;

/**
 * @author shimomo
 */
interface TsvDispatcher extends Tsv
{
    /**
     * @param ?string $value
     * @return list<list<string>>
     */
    public function parse(?string $value): array;
}
