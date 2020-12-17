<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrokenLinkChecker
{
    /** @var Crawler */
    private $crawler;

    /** @var string */
    private $uri;

    /** @var HttpClientInterface */
    private $client;

    public function __construct(Crawler $crawler, HttpClientInterface $client, ?string $uri = null)
    {
        $this->crawler = $crawler;
        $this->uri = $uri ?? '';
        $this->client = $client;
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
            $urls[$this->getStatusCode($link->getUri())][] = $link->getUri();
        }

        return [
            'urls' => $urls,
            'count' => \count($urls),
        ];
    }

    public function getStatusCode(string $uri): int
    {
        $response = $this->client->request(
            'GET',
            $uri
        );

        return $response->getStatusCode();
    }
}
