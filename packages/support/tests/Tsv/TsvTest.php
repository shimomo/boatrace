<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Tsv;

use Boatrace\Support\Tsv\Tsv;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class TsvTest extends TestCase
{
    /**
     * @param string $arguments
     * @param list<list<string>> $expected
     * @return void
     */
    #[Test]
    #[DataProviderExternal(TsvDataProvider::class, 'parseProvider')]
    public function parseReturnsRows(string $arguments, array $expected): void
    {
        $this->assertSame($expected, Tsv::parse($arguments)->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function parseReturnsEmptyArrayWhenGivenNull(): void
    {
        $this->assertSame([], Tsv::parse(null)->getValue());
    }
}
