<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\OptimizationChecker;

use Elao\Bundle\Accesseo\Checker\OptimizationChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class OptimizationCheckerTest extends TestCase
{
    public function testNoH1(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('no-h1.html');

        static::assertNull($optimizationChecker->getH1());
    }

    public function testEmptyH1(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('empty-h1.html');

        static::assertNull($optimizationChecker->getH1());
    }

    public function testNoTitle(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('no-title.html');

        static::assertNull($optimizationChecker->getTitle());
    }

    public function testNoOpenGraph(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('no-open-graph.html');

        static::assertEquals('missing', $optimizationChecker->getOpenGraphLevel());
    }

    public function testNoTwitterProperties(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('no-twitter-properties.html');

        static::assertEquals('missing', $optimizationChecker->getTwitterPropertiesLevel());
    }

    public function testAllIsComplete(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('all-is-complete.html');

        $twitterExpected = [
            'card' => 'summary',
            'title' => 'Twitter Title',
            'description' => 'Twitter Description',
            'site' => 'Twitter Site',
            'creator' => 'Twitter Creator',
            'image' => 'https://where-your-image-is-hosted/name.jpg',
        ];

        $openGraphExpected = [
            'title' => 'Title',
            'locale' => 'fr',
            'description' => 'description',
            'url' => 'http://localhost:8080/',
            'site_name' => 'name',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/Oliebollen.jpg',
        ];

        static::assertEquals('This is Title', $optimizationChecker->getTitle());
        static::assertEquals('This is meta description', $optimizationChecker->getMetaDescription());
        static::assertEquals('This is H1', $optimizationChecker->getH1());
        static::assertEquals('completed', $optimizationChecker->getTwitterPropertiesLevel());
        static::assertEquals('completed', $optimizationChecker->getOpenGraphLevel());
        static::assertIsArray($optimizationChecker->getTwitterProperties());
        static::assertEquals($twitterExpected, $optimizationChecker->getTwitterProperties());
        static::assertEquals($openGraphExpected, $optimizationChecker->getOpenGraphProperties());
    }

    public function testOgIsNotComplete(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('og-properties-missing.html');

        $openGraphExpected = [
            'url' => 'http://localhost:8080/',
            'site_name' => 'name',
        ];

        static::assertEquals($openGraphExpected, $optimizationChecker->getOpenGraphProperties());
        static::assertEquals('almost-completed', $optimizationChecker->getOpenGraphLevel());
    }

    public function testTwitterPropertiesAreNotCompleted(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('twitter-properties-missing.html');

        $propertiesExpected = [
            'card' => 'summary',
            'site' => 'Twitter Site',
            'creator' => 'Twitter Creator',
        ];

        static::assertEquals($propertiesExpected, $optimizationChecker->getTwitterProperties());
        static::assertEquals('almost-completed', $optimizationChecker->getTwitterPropertiesLevel());
    }

    public function testTwitterMissingProperties(): void
    {
        $missing = ['title', 'description', 'image'];
        $optimizationChecker = $this->getOptimizationChecker('twitter-properties-missing.html');
        static::assertEquals($missing, $optimizationChecker->getMissingTwitterProperties());
    }

    public function testTwitterNoMissingProperties(): void
    {
        $missing = [];
        $optimizationChecker = $this->getOptimizationChecker('all-is-complete.html');
        static::assertEquals($missing, $optimizationChecker->getMissingTwitterProperties());
    }

    public function testOGMissingProperties(): void
    {
        $missing = ['title', 'locale', 'description', 'image'];
        $optimizationChecker = $this->getOptimizationChecker('og-properties-missing.html');
        static::assertEquals($missing, $optimizationChecker->getMissingOGProperties());
    }

    public function testOGNoMissingProperties(): void
    {
        $missing = [];
        $optimizationChecker = $this->getOptimizationChecker('all-is-complete.html');
        static::assertEquals($missing, $optimizationChecker->getMissingOGProperties());
    }

    public function test2H1(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('2-h1.html');
        static::assertEquals(true, $optimizationChecker->atLeastOneH1());
    }

    public function testOneH1(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('all-is-complete.html');
        static::assertEquals(true, $optimizationChecker->atLeastOneH1());
    }

    public function testOneH1Null(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('no-title.html');
        static::assertEquals(false, $optimizationChecker->atLeastOneH1());
    }

    public function testGetHrefLang(): void
    {
        $expect = [
          ['hreflang' => 'en-gb', 'href' => 'http://en-gb.example.com/page.html'],
          ['hreflang' => 'en-us', 'href' => 'http://en-us.example.com/page.html'],
          ['hreflang' => 'en', 'href' => 'http://en.example.com/page.html'],
          ['hreflang' => 'de', 'href' => 'http://de.example.com/page.html'],
          ['hreflang' => 'x-default', 'href' => 'http://www.example.com/'],
        ];
        $optimizationChecker = $this->getOptimizationChecker('hreflang.html');
        static::assertEquals($expect, $optimizationChecker->getHrefLang());
    }

    public function testIsHrefLang(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('hreflang.html');
        static::assertEquals(true, $optimizationChecker->isHreflang());
    }

    public function testIsNoHrefLang(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('no-title.html');
        static::assertEquals(false, $optimizationChecker->isHreflang());
    }

    public function testMicrodataHTML(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('microdata-html.html');

        static::assertEquals(3, \count($optimizationChecker->getAllMicroData()));
    }

    public function testMicrodataJson(): void
    {
        $optimizationChecker = $this->getOptimizationChecker('microdata-json.html');

        static::assertEquals(1, \count($optimizationChecker->getAllMicroData()));
    }

    public function getOptimizationChecker($filename): OptimizationChecker
    {
        $html = file_get_contents(sprintf(__DIR__.'/../OptimizationChecker/%s', $filename));
        $crawler = new Crawler($html);

        return new OptimizationChecker($crawler);
    }
}
