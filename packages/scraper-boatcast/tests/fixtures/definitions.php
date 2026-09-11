<?php

declare(strict_types=1);

use Boatrace\Support\Throttler\ThrottlerDispatcher;
use Boatrace\Types\Contracts\Converter\ConverterDispatcher as ConverterDispatcherContract;
use Boatrace\Types\Contracts\Throttler\ThrottlerDispatcher as ThrottlerDispatcherContract;

return [
    ThrottlerDispatcherContract::class => \DI\autowire(ThrottlerDispatcher::class)
        ->constructor(\DI\get(ConverterDispatcherContract::class), 0.0),
];
