<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\BrokenLinkChecker;

use Elao\Bundle\Accesseo\Checker\BrokenLinkChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BrokenLinkCheckerTest extends TestCase
{
    /** @var MockClientCallback */
    private $responsesFactory;

    /** @var BrokenLinkChecker */
    private $checker;

    protected function setUp(): void
    {
        $httpClient = new MockHttpClient($this->responsesFactory = new MockClientCallback());
        $this->checker = new BrokenLinkChecker($httpClient);
    }

    /**
     * @dataProvider provideGetLinksInformationsData
     */
    public function testGetLinksInformations(string $filename, array $responses, array $expected): void
    {
        $html = file_get_contents(sprintf(__DIR__.'/../BrokenLinkChecker/%s', $filename));

        $this->responsesFactory->addResponses(...array_values($responses));

        static::assertEquals($expected, $this->checker->getExternalBrokenLinks(new Crawler($html)));
    }

    public function provideGetLinksInformationsData(): iterable
    {
        yield [
            'filename' => 'broken-links.html',
            'responses' => [
                'http://www.google.com' => ['info' => ['http_code' => 200]],
                'https://www.nasa.gov/' => ['info' => ['http_code' => 200]],
                'http://www.google.com/error' => ['info' => ['http_code' => 404]],
                'http://www.google/error' => new TransportException('invalid url'),
                'http://www.google/timeout' => new TimeoutException('timeout url'),
            ],
            'expected' => [
                    '200' => ['http://www.google.com', 'https://www.nasa.gov/'],
                    '404' => ['http://www.google.com/error'],
                    'invalid' => ['http://www.google/error'],
                    'timeout' => ['http://www.google/timeout'],
            ],
        ];
    }
}

class MockClientCallback
{
    /** @var array|ResponseInterface */
    private $responses = [];

    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        if (false === $data = current($this->responses)) {
            throw new \LogicException('No more mock response available');
        }

        next($this->responses);

        if ($data instanceof TransportException) {
            throw $data;
        }

        return $data instanceof ResponseInterface ? $data : new MockResponse($data['body'] ?? '', $data['info'] ?? []);
    }

    public function addResponses(...$responses): void
    {
        foreach ($responses as $response) {
            $this->responses[] = $response;
        }
    }
}
