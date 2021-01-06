<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;

class BrokenLinkChecker
{
    /** @var Crawler */
    private $crawler;

    /** @var string */
    private $uri;

    public function __construct(Crawler $crawler, ?string $uri = null)
    {
        $this->crawler = $crawler;
        $this->uri = $uri ?? '';
    }

    public function getExternalBrokenLinks(): ?array
    {
        $urls = [];

        $links = $this->crawler->filter('a')->links();

        if (\count($links) === 0) {
            return [
                'urls' => [],
                'count' => 0,
            ];
        }

        foreach ($links as $link) {
            if (false !== strpos($link->getUri(), $this->uri)) {
                continue;
            }

            $urls[$this->getStatusCode($link->getUri())][] = $link->getUri();
        }

        return [
            'urls' => $urls,
            'count' => \count($urls),
        ];
    }

    public function getStatusCode(string $uri): int
    {
        $ch = curl_init($uri);

        if ($ch === false) {
            return 0;
        }

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $retcode;
    }
}
