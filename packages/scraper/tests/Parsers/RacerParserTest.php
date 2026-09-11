<?php

declare(strict_types=1);

namespace Boatrace\Scraper\Tests\Parsers;

use BadMethodCallException;
use Boatrace\Scraper\Parsers\RacerParser;
use Boatrace\Support\Converter\ConverterDispatcher;
use Boatrace\Support\Trimmer\TrimmerDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class RacerParserTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Scraper\Parsers\RacerParser
     */
    protected RacerParser $racerParser;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->racerParser = new RacerParser(new ConverterDispatcher(new TrimmerDispatcher()));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseNameReturnsName(): void
    {
        $this->assertSame(['name' => '金田 諭'], $this->racerParser->parseName('金田　　　諭'));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseNameRestoresMappedName(): void
    {
        $this->assertSame(
            ['name' => 'マイケル 田代'],
            $this->racerParser->parseName('マイケル田代')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function parseNameKeepsNameThatIsNotMapped(): void
    {
        $this->assertSame(['name' => '未知乃選手'], $this->racerParser->parseName('未知乃選手'));
    }

    /**
     * @return void
     */
    #[Test]
    public function parseNameReturnsNullWhenValueIsEmpty(): void
    {
        $this->assertSame(['name' => null], $this->racerParser->parseName(null));
        $this->assertSame(['name' => null], $this->racerParser->parseName(''));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Scraper\Parsers\RacerParser::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->racerParser->ghost();
    }
}
