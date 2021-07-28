<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;

class LinkChecker
{
    /** @var Crawler */
    private $crawler;

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function getQualifiedOutboundLinks(): ?array
    {
        $qualifiedOutboundLinks = [];

        $links = $this->crawler
            ->filter('a')
            ->extract(['href', '_text', 'rel']);

        foreach ($links as $link) {
            if (!preg_match('[mailto://|tel://]', $link[0])) {
                $qualifiedOutboundLinks[] =
                    [
                        'href' => $link[0],
                        'text' => $link[1],
                        'rel' => $link[2],
                    ];
            }
        }

        return $qualifiedOutboundLinks;
    }
}
