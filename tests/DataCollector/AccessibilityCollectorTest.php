<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\DataCollector;

use Elao\Bundle\Accesseo\DataCollector\AccessibilityCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessibilityCollectorTest extends TestCase
{
    public function testGetData(): void
    {
        $html = file_get_contents(__DIR__.'/../DataCollector/my-page.html');
        $request = new Request();
        $response = new Response($html, 200);
        $datacollector = new AccessibilityCollector(['icon']);
        $datacollector->collect($request, $response);

        static::assertEquals(2, $datacollector->getCountAllIcons());
        static::assertEquals(1, $datacollector->getCountAllExplicitIcons());
        static::assertEquals(0, $datacollector->getCountAllImages());
        static::assertEquals(0, $datacollector->getCountAltFromImages());
    }
}
