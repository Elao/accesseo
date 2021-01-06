<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class RobotDirectivesChecker
{
    /** @var Crawler */
    public $crawler;

    /** @var Response */
    public $response;

    public function __construct(Crawler $crawler, Response $response)
    {
        $this->crawler = $crawler;
        $this->response = $response;
    }

    public function getXRobotsTag(): ?string
    {
        try {
            return $this->response->headers->get('X-Robots-Tag');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getLanguage(): ?string
    {
        try {
            return $this->crawler->filter('html')->attr('lang');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCanonical(): ?string
    {
        try {
            return $this->crawler->filter('head > link[rel="canonical"]')->attr('href');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getMetaRobotsTag(): ?string
    {
        try {
            return $this->crawler->filter('head > meta[name="robots"]')->first()->attr('content');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getMetaGooglebotsTag(): ?string
    {
        try {
            return $this->crawler->filter('head > meta[name="googlebot"]')->first()->attr('content');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getMetaGooglebotNewsTag(): ?string
    {
        try {
            return $this->crawler->filter('head > meta[name="googlebot-news"]')->first()->attr('content');
        } catch (\Exception $e) {
            return null;
        }
    }
}
