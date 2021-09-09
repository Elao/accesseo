<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\DataCollector;

use Elao\Bundle\Accesseo\Checker\OptimizationChecker;
use Elao\Bundle\Accesseo\DataCollector\SeoCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataCollectorTest extends TestCase
{
    public function testGetData(): void
    {
        $html = file_get_contents(__DIR__.'/../DataCollector/my-page.html');
        $request = new Request();
        $response = new Response($html, 200);
        $datacollector = new SeoCollector(['icon']);
        $datacollector->collect($request, $response);
        $datacollector->lateCollect();

        static::assertEquals('This is H1', $datacollector->getH1());
        static::assertEquals('en', $datacollector->getLanguage());
        static::assertEquals(['title', 'description', 'image'], $datacollector->getMissingTwitterProperties());
        static::assertEquals(OptimizationChecker::OG_PROPERTIES, $datacollector->getMissingOpenGraphProperties());
        static::assertEquals(true, $datacollector->getAtLeastOneH1());
        static::assertEquals([], $datacollector->getOpenGraphProperties());
        static::assertEquals(['card' => 'summary', 'site' => 'Twitter Site', 'creator' => 'Twitter Creator'], $datacollector->getTwitterProperties());
        static::assertEquals('Title', $datacollector->getTitle());
        static::assertEquals('This is meta description', $datacollector->getMetaDescription());
        static::assertEquals('missing', $datacollector->getOpenGraphLevel());
        static::assertEquals('almost-completed', $datacollector->getTwitterPropertiesLevel());
        static::assertEquals(null, $datacollector->getXRobotsTag());
        static::assertEquals(null, $datacollector->getCanonical());
        static::assertEquals(null, $datacollector->getMetaRobot());
        static::assertEquals(null, $datacollector->getMetaGooglebot());
        static::assertEquals(null, $datacollector->getMetaGooglebotNews());
        static::assertEquals(['h1' => 1, 'h2' => 0, 'h3' => 0, 'h4' => 0, 'h5' => 0, 'h6' => 0], $datacollector->getCountHeadlines());
    }
}
