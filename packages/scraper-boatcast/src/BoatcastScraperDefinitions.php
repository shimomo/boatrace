<?php

declare(strict_types=1);

namespace Boatrace\BoatcastScraper;

use Boatrace\Types\Contracts\Definitions\Definitions as DefinitionsContract;

/**
 * @author shimomo
 */
final class BoatcastScraperDefinitions implements DefinitionsContract
{
    /**
     * @return non-empty-string
     */
    #[\Override]
    public static function definitions(): string
    {
        return __DIR__ . '/../config/definitions.php';
    }
}
