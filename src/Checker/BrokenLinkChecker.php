<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrokenLinkChecker
{
    const STATUS_CODE_REDIRECTS = [301, 308, 302, 303, 307, 300, 304];
    const STATUS_CODE_SUCCESS = [200];

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
        if (\count($links) === 0) {
            return [];
        }
        $urls = [
            'errors' => [],
            'redirections' => [],
            'success' => [],
        ];

        foreach ($links as $link) {
            try {
                $status = $this->getStatusCode($link);
            } catch (TimeoutExceptionInterface $ex) {
                $status = 'timeout';
            } catch (TransportExceptionInterface $ex) {
                $status = 'invalid';
            }

            switch ($status) {
                case (in_array($status, self::STATUS_CODE_REDIRECTS)):
                    $urls['redirections'][$status][] = $link;
                    break;
                case (in_array($status, self::STATUS_CODE_SUCCESS)):
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
