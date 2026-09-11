<?php

declare(strict_types=1);

namespace Boatrace\Types\Contracts\Core;

use DI\Container;

/**
 * @author shimomo
 */
interface CoreContainer extends Core
{
    /**
     * @template T of object
     * @param class-string<T> $name
     * @return T
     */
    public static function getInstance(string $name): object;

    /**
     * @return \DI\Container
     */
    public static function getContainer(): Container;

    /**
     * @param non-empty-string ...$paths
     * @return void
     */
    public static function addDefinitions(string ...$paths): void;
}
