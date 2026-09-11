<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Json;

use BadMethodCallException;
use Boatrace\Support\Json\JsonDispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author shimomo
 */
final class JsonDispatcherTest extends TestCase
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     * @var \Boatrace\Support\Json\JsonDispatcher
     */
    protected JsonDispatcher $json;

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        $this->json = new JsonDispatcher();
    }

    /**
     * @return void
     */
    #[Test]
    public function decodeReturnsArray(): void
    {
        $this->assertSame(
            ['res_cd' => 0, 'return_info' => [['RaceStudiumNo' => '22']]],
            $this->json->decode('{"res_cd":0,"return_info":[{"RaceStudiumNo":"22"}]}')
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function decodeReturnsNullWhenGivenNullOrEmptyString(): void
    {
        $this->assertNull($this->json->decode(null));
        $this->assertNull($this->json->decode(''));
    }

    /**
     * @return void
     */
    #[Test]
    public function decodeReturnsNullWhenGivenBrokenJson(): void
    {
        $this->assertNull($this->json->decode('{"res_cd":'));
        $this->assertNull($this->json->decode('<!doctype html>'));
    }

    /**
     * @return void
     */
    #[Test]
    public function decodeReturnsNullWhenGivenScalar(): void
    {
        $this->assertNull($this->json->decode('1'));
        $this->assertNull($this->json->decode('"競艇"'));
    }

    /**
     * @return void
     */
    #[Test]
    public function throwsBadMethodCallExceptionWhenCallingUndefinedMethod(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessageIs(
            'Call to undefined method `Boatrace\Support\Json\JsonDispatcher::ghost()`.'
        );

        /** @psalm-suppress UndefinedMagicMethod */
        $this->json->ghost();
    }
}
