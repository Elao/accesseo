<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\LinkChecker;

use Elao\Bundle\Accesseo\Checker\LinkChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class LinkCheckerTest extends TestCase
{
    public function testListImagesWithoutAriaHidden(): void
    {
        $linkChecker = $this->getLinkChecker('links-mix-rel.html');

        $expected = [
            [
                'href' => 'https://cheese.example.com/Appenzeller_cheese',
                'text' => 'appenzeller',
                'rel' => 'ugc nofollow',
            ],
            [
                'href' => 'https://cheese.example.com//blue_cheese',
                'text' => 'à pâte persillée',
                'rel' => 'ugc,nofollow',
            ],
            [
                'href' => 'https://cheese.example.com//blue_cheese',
                'text' => 'à pâte persillée',
                'rel' => '',
            ],
            [
                'href' => '/blue_cheese',
                'text' => 'Blue Cheese',
                'rel' => '',
            ],
        ];

        static::assertEquals($expected, $linkChecker->getQualifiedOutboundLinks());
    }

    public function getLinkChecker($filename): LinkChecker
    {
        $html = file_get_contents(sprintf(__DIR__.'/../LinkChecker/%s', $filename));
        $crawler = new Crawler($html);

        return new LinkChecker($crawler);
    }
}
