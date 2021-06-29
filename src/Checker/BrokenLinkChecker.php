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

    public function getLinks(Crawler $crawler): ?array
    {
        return $crawler
            ->filter('a')
            ->extract(['href']);
    }

    public function getExternalBrokenLinks(array $links): ?array
    {
        $urls = [
            'errors' => [
                '403' => [],
                '404' => [],
                '500' => [],
                '503' => [],
                '504' => [],
            ],
            'redirections' => [
                '301' => [],
                '302' => [],
            ],
            'success' => [
                '200' => [],
            ],
        ];

        if (\count($links) === 0) {
            return $urls;
        }

        foreach ($links as $link) {
            try {
                $status = $this->getStatusCode($link);
            } catch (TimeoutExceptionInterface $ex) {
                $status = 'timeout';
            } catch (TransportExceptionInterface $ex) {
                $status = 'invalid';
            }

            switch (true) {
                case $status >= 300 && $status < 400:
                    $urls['redirections'][$status][] = $link;
                    break;
                case $status === 200:
                    $urls['success'][$status][] = $link;
                    break;
                default:
                    $urls['errors'][$status][] = $link;
            }
        }

        return $urls;
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
