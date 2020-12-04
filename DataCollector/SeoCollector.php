<?php

declare(strict_types=1);

namespace Elao\Bundle\SeoTool\DataCollector;

use Elao\Bundle\SeoTool\Checker\AccessibilityChecker;
use Elao\Bundle\SeoTool\Checker\BrokenLinkChecker;
use Elao\Bundle\SeoTool\Checker\ImageChecker;
use Elao\Bundle\SeoTool\Checker\OptimizationChecker;
use Elao\Bundle\SeoTool\Checker\RobotDirectivesChecker;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

class SeoCollector extends DataCollector implements LateDataCollectorInterface
{
    /** @var ImageChecker */
    public $imageChecker;

    /** @var OptimizationChecker */
    public $optimizationChecker;

    /** @var AccessibilityChecker */
    public $accessbilityChecker;

    /** @var RobotDirectivesChecker */
    public $robotDirectivesChecker;

    /** @var BrokenLinkChecker */
    public $brokenLinkChecker;

    public function getName(): string
    {
        return 'elao.seo_tool.seo_collector';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $crawler = new Crawler((string) $response->getContent(), $request->getUri(), $request->getBaseUrl());

        $this->imageChecker = new ImageChecker($crawler);
        $this->optimizationChecker = new OptimizationChecker($crawler);
        $this->accessbilityChecker = new AccessibilityChecker($crawler);
        $this->brokenLinkChecker = new BrokenLinkChecker($crawler, $request->getUri());

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
            'countHeadlines' => $this->accessbilityChecker->countHeadlinesByHn(),
            'headlinesTree' => $this->accessbilityChecker->getHeadlineTree(),
            'externalBrokenLinks' => $this->brokenLinkChecker->getExternalBrokenLinks(),
            'isHreflang' => $this->optimizationChecker->isHreflang(),
            'hreflang' => $this->optimizationChecker->getHreflang(),
        ];
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
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getHreflang(): array
    {
        return $this->data['hreflang'];
    }

    public function isHreflang(): bool
    {
        return $this->data['isHreflang'];
    }

    public function getExternalBrokenLinks(): array
    {
        return $this->data['externalBrokenLinks'];
    }

    public function getHeadlinesTree(): array
    {
        return $this->data['headlinesTree'];
    }

    public function getLanguage(): ?string
    {
        return $this->data['language'];
    }

    public function getCountHeadlines(): array
    {
        return $this->data['countHeadlines'];
    }

    public function getMissingTwitterProperties(): array
    {
        return $this->data['missingTwitterProperties'];
    }

    public function getMissingOpenGraphProperties(): array
    {
        return $this->data['missingOpenGraphProperties'];
    }

    public function getAtLeastOneH1(): bool
    {
        return $this->data['atLeastOneH1'];
    }

    public function getOpenGraphProperties(): array
    {
        return $this->data['OpenGraphProperties'];
    }

    public function getTwitterProperties(): array
    {
        return $this->data['twitterProperties'];
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
