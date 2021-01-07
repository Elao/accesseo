<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\DataCollector;

use Elao\Bundle\Accesseo\Checker\AccessibilityChecker;
use Elao\Bundle\Accesseo\Checker\ImageChecker;
use Elao\Bundle\Accesseo\Checker\OptimizationChecker;
use Elao\Bundle\Accesseo\Checker\RobotDirectivesChecker;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\VarDumper\Cloner\Data;

class SeoCollector extends DataCollector implements LateDataCollectorInterface
{
    /** @var ImageChecker */
    public $imageChecker;

    /** @var OptimizationChecker */
    public $optimizationChecker;

    /** @var AccessibilityChecker */
    public $accessibilityChecker;

    /** @var RobotDirectivesChecker */
    public $robotDirectivesChecker;

    /** @var Stopwatch|null */
    private $stopwatch;

    public function __construct(?Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public function getName(): string
    {
        return 'elao.accesseo.seo_collector';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        if ($this->stopwatch) {
            $event = $this->stopwatch->start('seo-collect', 'accesseo');
        }

        $crawler = new Crawler((string) $response->getContent(), $request->getUri(), $request->getBaseUrl());

        $this->imageChecker = new ImageChecker($crawler);
        $this->optimizationChecker = new OptimizationChecker($crawler);
        $this->accessibilityChecker = new AccessibilityChecker($crawler);

        $this->data = [
            'response' => $response,
            'title' => $this->optimizationChecker->getTitle(),
            'metaDescription' => $this->optimizationChecker->getMetaDescription(),
            'h1' => $this->optimizationChecker->getH1(),
            'atLeastOneH1' => $this->optimizationChecker->atLeastOneH1(),
            'OpenGraphLevel' => $this->optimizationChecker->getOpenGraphLevel(),
            'twitterPropertiesLevel' => $this->optimizationChecker->getTwitterPropertiesLevel(),
            'twitterProperties' => $this->optimizationChecker->getTwitterProperties(),
            'OpenGraphProperties' => $this->optimizationChecker->getOpenGraphProperties(),
            'missingOpenGraphProperties' => $this->optimizationChecker->getMissingOGProperties(),
            'missingTwitterProperties' => $this->optimizationChecker->getMissingTwitterProperties(),
            'countHeadlines' => $this->accessibilityChecker->countHeadlinesByHn(),
            'headlinesTree' => $this->accessibilityChecker->getHeadlineTree(),
            'isHreflang' => $this->optimizationChecker->isHreflang(),
            'hreflang' => $this->optimizationChecker->getHreflang(),
        ];

        if (isset($event)) {
            $event->stop();
        }
    }

    public function lateCollect(): void
    {
        $response = $this->data['response'];
        $this->robotDirectivesChecker = new RobotDirectivesChecker(new Crawler((string) $response->getContent(), 'text/html'), $response);
        $this->data['XRobotsTag'] = $this->robotDirectivesChecker->getXRobotsTag();
        $this->data['canonical'] = $this->robotDirectivesChecker->getCanonical();
        $this->data['metaRobot'] = $this->robotDirectivesChecker->getMetaRobotsTag();
        $this->data['metaGooglebot'] = $this->robotDirectivesChecker->getMetaGooglebotsTag();
        $this->data['metaGooglebotNews'] = $this->robotDirectivesChecker->getMetaGooglebotNewsTag();
        $this->data['language'] = $this->robotDirectivesChecker->getLanguage();

        $this->data = $this->cloneVar($this->data);
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getData()
    {
        return $this->data;
    }

    public function seek(string $key): Data
    {
        return $this->data->seek($key);
    }

    public function getHreflang(): array
    {
        return $this->data['hreflang']->getValue();
    }

    public function isHreflang(): bool
    {
        return $this->data['isHreflang'];
    }

    public function getHeadlinesTree(): array
    {
        return $this->data['headlinesTree']->getValue();
    }

    public function getLanguage(): ?string
    {
        return $this->data['language'];
    }

    public function getCountHeadlines(): array
    {
        return array_map(static function (Data $data): int {
            return $data->getValue();
        }, $this->data['countHeadlines']->getValue());
    }

    public function getMissingTwitterProperties(): array
    {
        return $this->data['missingTwitterProperties']->getValue();
    }

    public function getMissingOpenGraphProperties(): array
    {
        return $this->data['missingOpenGraphProperties']->getValue();
    }

    public function getAtLeastOneH1(): bool
    {
        return $this->data['atLeastOneH1'];
    }

    public function getOpenGraphProperties(): array
    {
        return $this->data['OpenGraphProperties']->getValue();
    }

    public function getTwitterProperties(): array
    {
        return $this->data['twitterProperties']->getValue();
    }

    public function getTitle(): ?string
    {
        return $this->data['title'];
    }

    public function getH1(): ?string
    {
        return $this->data['h1'];
    }

    public function getMetaDescription(): ?string
    {
        return $this->data['metaDescription'];
    }

    public function getOpenGraphLevel(): string
    {
        return $this->data['OpenGraphLevel'];
    }

    public function getTwitterPropertiesLevel(): string
    {
        return $this->data['twitterPropertiesLevel'];
    }

    public function getXRobotsTag(): ?string
    {
        return $this->data['XRobotsTag'];
    }

    public function getCanonical(): ?string
    {
        return $this->data['canonical'];
    }

    public function getMetaRobot(): ?string
    {
        return $this->data['metaRobot'];
    }

    public function getMetaGooglebot(): ?string
    {
        return $this->data['metaGooglebot'];
    }

    public function getMetaGooglebotNews(): ?string
    {
        return $this->data['metaGooglebotNews'];
    }
}
