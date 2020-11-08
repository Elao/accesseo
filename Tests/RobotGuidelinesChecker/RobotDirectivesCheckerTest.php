<?php

declare(strict_types=1);

use Elao\Bundle\SEOTool\Checker\RobotDirectivesChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class RobotDirectivesCheckerTest extends TestCase
{
    public function testCanonical()
    {
        $checker = $this->getRobotGuidelinesChecker('with-canonical.html');
        static::assertEquals('http://localhost:8080/', $checker->getCanonical());
    }

    public function testNoCanonical()
    {
        $checker = $this->getRobotGuidelinesChecker('no-canonical.html');
        static::assertNull($checker->getCanonical());
    }

    public function testXRobotsTagNoIndex()
    {
        $checker = new RobotDirectivesChecker(new Crawler(), new Response('', 200, ['X-Robots-Tag' => 'noindex']));
        static::assertEquals('noindex', $checker->getXRobotsTag());
    }

    public function testXRobotsTagEmpty()
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

    public function testGetMetaRobotTag()
    {
        $checker = $this->getRobotGuidelinesChecker('meta-robots.html');
        static::assertEquals('noindex', $checker->getMetaRobotsTag());
    }

    public function testGetMetaGooglebotTag()
    {
        $checker = $this->getRobotGuidelinesChecker('meta-robots.html');
        static::assertEquals('noindex', $checker->getMetaGooglebotsTag());
    }

    public function testGetMetaGooglebotNewsTag()
    {
        $checker = $this->getRobotGuidelinesChecker('meta-robots.html');
        static::assertEquals('noindex', $checker->getMetaGooglebotNewsTag());
    }

    public function testGetLanguage()
    {
        $checker = $this->getRobotGuidelinesChecker('with-canonical.html');
        static::assertEquals('en', $checker->getLanguage());
    }
}
