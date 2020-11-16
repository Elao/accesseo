<?php

declare(strict_types=1);

use Elao\Bundle\SEOTool\Checker\BrokenLinkChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class BrokenLinkCheckerTest extends TestCase
{
    public function testGetLinksInformations()
    {
        $expected = [
            'urls' => [
                '200' => ['http://www.google.com', 'https://www.nasa.gov/'],
                '404' => ['http://www.google.com/error'],
                '0' => ['http://www.google/error']
            ],
            'count' => 3
        ];

        $brokenlinkchecker = $this->getBrokenLinkChecker('broken-links.html');

        static::assertEquals($expected, $brokenlinkchecker->getExternalBrokenLinks());
    }

    public function getBrokenLinkChecker($filename): BrokenLinkChecker
    {
        $html = file_get_contents(sprintf('Tests/BrokenLinkChecker/%s', $filename));
        $crawler = new Crawler($html);

        return new BrokenLinkChecker($crawler, 'http://localhost');
    }
}
