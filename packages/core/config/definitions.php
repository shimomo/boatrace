<?php

declare(strict_types=1);

use Boatrace\Core\Core;
use Boatrace\Core\CoreDispatcher;
use Boatrace\Core\CoreResponse;
use Boatrace\Types\Contracts\Core\Core as CoreContract;
use Boatrace\Types\Contracts\Core\CoreDispatcher as CoreDispatcherContract;
use Boatrace\Types\Contracts\Core\CoreResponse as CoreResponseContract;

return [
    CoreContract::class => \DI\autowire(Core::class)->constructor(\DI\get(CoreDispatcherContract::class)),
    CoreDispatcherContract::class => \DI\autowire(CoreDispatcher::class),
    CoreResponseContract::class => function (\DI\Container $container, ?string $value = null) {
        return new CoreResponse($value);
    },
];
