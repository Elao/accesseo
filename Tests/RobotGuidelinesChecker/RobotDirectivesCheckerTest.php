<?php

declare(strict_types=1);

namespace Elao\Bundle\SeoTool\Tests\RobotGuidelinesChecker;

use Elao\Bundle\SeoTool\Checker\RobotDirectivesChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class RobotDirectivesCheckerTest extends TestCase
{
    public function testCanonical(): void
    {
        $checker = $this->getRobotGuidelinesChecker('with-canonical.html');
        static::assertEquals('http://localhost:8080/', $checker->getCanonical());
    }

    public function testNoCanonical(): void
    {
        $checker = $this->getRobotGuidelinesChecker('no-canonical.html');
        static::assertNull($checker->getCanonical());
    }

    public function testXRobotsTagNoIndex(): void
    {
        $checker = new RobotDirectivesChecker(new Crawler(), new Response('', 200, ['X-Robots-Tag' => 'noindex']));
        static::assertEquals('noindex', $checker->getXRobotsTag());
    }

    public function testXRobotsTagEmpty(): void
    {
        $checker = new RobotDirectivesChecker(new Crawler(), new Response('', 200, []));
        static::assertEquals(null, $checker->getXRobotsTag());
    }

    public function getRobotGuidelinesChecker($filename): RobotDirectivesChecker
    {
        $html = file_get_contents(sprintf('Tests/RobotGuidelinesChecker/%s', $filename));
        $crawler = new Crawler($html);

        return new RobotDirectivesChecker($crawler, new Response());
    }

    public function testGetMetaRobotTag(): void
    {
        $checker = $this->getRobotGuidelinesChecker('meta-robots.html');
        static::assertEquals('noindex', $checker->getMetaRobotsTag());
    }

    public function testGetMetaGooglebotTag(): void
    {
        $checker = $this->getRobotGuidelinesChecker('meta-robots.html');
        static::assertEquals('noindex', $checker->getMetaGooglebotsTag());
    }

    public function testGetMetaGooglebotNewsTag(): void
    {
        $checker = $this->getRobotGuidelinesChecker('meta-robots.html');
        static::assertEquals('noindex', $checker->getMetaGooglebotNewsTag());
    }

    public function testGetLanguage(): void
    {
        $checker = $this->getRobotGuidelinesChecker('with-canonical.html');
        static::assertEquals('en', $checker->getLanguage());
    }
}
