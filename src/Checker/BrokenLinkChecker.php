<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrokenLinkChecker
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getExternalBrokenLinks(Crawler $crawler): ?array
    {
        $urls = [];

        $links = $crawler->filter('a')->links();

        if (\count($links) === 0) {
            return [
                'urls' => [],
                'count' => 0,
            ];
        }

        foreach ($links as $link) {
            $uri = $link->getUri();

            try {
                $status = $this->getStatusCode($uri);
            } catch (TimeoutExceptionInterface $ex) {
                $status = 'timeout';
            } catch (TransportExceptionInterface $ex) {
                $status = 'invalid';
            }

            $urls[$status][] = $uri;
        }

        return [
            'urls' => $urls,
            'count' => \count($urls),
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function getStatusCode(string $uri): int
    {
        $response = $this->client->request(
            'GET',
            $uri
        );

        return $response->getStatusCode();
    }
}
