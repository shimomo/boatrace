<?php

declare(strict_types=1);

namespace Boatrace\Support\Tests\Browser;

use Boatrace\Support\Browser\Browser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class BrowserTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function createReturnsHttpBrowser(): void
    {
        $this->assertInstanceOf(HttpBrowser::class, Browser::create()->getValue());
    }

    /**
     * @return void
     */
    #[Test]
    public function createReturnsNewHttpBrowserEachTime(): void
    {
        $this->assertNotSame(Browser::create()->getValue(), Browser::create()->getValue());
    }
}
