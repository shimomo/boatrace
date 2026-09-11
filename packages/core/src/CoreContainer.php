<?php

declare(strict_types=1);

namespace Boatrace\Core;

use Boatrace\Types\Contracts\Core\CoreContainer as CoreContainerContract;
use Boatrace\Types\Contracts\Definitions\Definitions as DefinitionsContract;
use DI\Container;
use DI\ContainerBuilder;
use LogicException;

/**
 * @author shimomo
 */
final class CoreContainer implements CoreContainerContract
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array PROVIDERS = [
        CoreDefinitions::class,
        'Boatrace\Support\SupportDefinitions',
        'Boatrace\Scraper\ScraperDefinitions',
        'Boatrace\BoatcastScraper\BoatcastScraperDefinitions',
    ];

    /**
     * @var array<class-string, object>
     */
    private static array $instances = [];

    /**
     * @var list<non-empty-string>
     */
    private static array $paths = [];

    /**
     * @var ?\DI\Container
     */
    private static ?Container $container = null;

    /**
     * @template T of object
     * @param class-string<T> $name
     * @return T
     * @throws \LogicException
     */
    #[\Override]
    public static function getInstance(string $name): object
    {
        $instance = self::$instances[$name] ?? self::getContainer()->get($name);

        if (!$instance instanceof $name) {
            throw new LogicException(
                sprintf('Expected `%s`, got `%s`.', $name, get_debug_type($instance))
            );
        }

        return self::$instances[$name] = $instance;
    }

    /**
     * @return \DI\Container
     */
    #[\Override]
    public static function getContainer(): Container
    {
        return self::$container ??= (function (): Container {
            $containerBuilder = new ContainerBuilder();

            foreach (self::getDefinitions() as $path) {
                $containerBuilder->addDefinitions($path);
            }

            return $containerBuilder->build();
        })();
    }

    /**
     * @param non-empty-string ...$paths
     * @return void
     */
    #[\Override]
    public static function addDefinitions(string ...$paths): void
    {
        foreach ($paths as $path) {
            if (!in_array($path, self::$paths, true)) {
                self::$paths[] = $path;
            }
        }

        self::$instances = [];
        self::$container = null;
    }

    /**
     * @return list<non-empty-string>
     */
    private static function getDefinitions(): array
    {
        $paths = [];

        foreach (self::PROVIDERS as $provider) {
            if (!class_exists($provider) || !is_subclass_of($provider, DefinitionsContract::class)) {
                continue;
            }

            $paths[] = $provider::definitions();
        }

        return [...$paths, ...self::$paths];
    }
}
