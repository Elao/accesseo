<?php

declare(strict_types=1);

use Elao\Bundle\SEOTool\DataCollector\AccessibilityCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessibilityCollectorTest extends TestCase
{
    public function testGetData()
    {
        $html = file_get_contents('Tests/DataCollector/my-page.html');
        $request = new Request();
        $response = new Response($html, 200);
        $datacollector = new AccessibilityCollector();
        $datacollector->collect($request, $response);

        static::assertEquals([], $datacollector->listMissingAltFromImages());
        static::assertEquals(['icon icon--alert'], $datacollector->listNonExplicitIcons());
        static::assertEquals(2, $datacollector->getCountAllIcons());
        static::assertEquals(1, $datacollector->getCountAllExplicitIcons());
        static::assertEquals(0, $datacollector->getCountAllImages());
        static::assertEquals(0, $datacollector->getCountAltFromImages());
    }
}
