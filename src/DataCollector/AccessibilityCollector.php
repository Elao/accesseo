<?php

declare(strict_types=1);

namespace Elao\Bundle\SeoTool\DataCollector;

use Elao\Bundle\SeoTool\Checker\AccessibilityChecker;
use Elao\Bundle\SeoTool\Checker\BrokenLinkChecker;
use Elao\Bundle\SeoTool\Checker\ImageChecker;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Stopwatch\Stopwatch;

class AccessibilityCollector extends DataCollector
{
    /** @var ImageChecker */
    public $imageChecker;

    /** @var AccessibilityChecker */
    public $accessibilityChecker;

    /** @var BrokenLinkChecker */
    public $brokenLinkChecker;

    /** @var Stopwatch|null */
    private $stopwatch;

    public function __construct(?Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public function getName(): string
    {
        return 'elao.seo_tool.accessibility_collector';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        if ($this->stopwatch) {
            $event = $this->stopwatch->start('accessibility-collect', 'seo-tool');
        }

        $crawler = new Crawler((string) $response->getContent(), $request->getUri(), $request->getBaseUrl());

        $this->imageChecker = new ImageChecker($crawler);
        $this->accessibilityChecker = new AccessibilityChecker($crawler);
        $this->brokenLinkChecker = new BrokenLinkChecker($crawler, $request->getUri());

        $this->data = [
            'response' => $response,
            'countAllImages' => $this->imageChecker->countAllImages(),
            'countAltFromImages' => $this->imageChecker->countAltFromImages(),
            'listMissingAltFromImages' => $this->imageChecker->listImagesWhithoutAlt(),
            'listNonExplicitIcons' => $this->imageChecker->listNonExplicitIcons(),
            'countAllIcons' => $this->imageChecker->countIcons(),
            'countAllExplicitIcons' => $this->imageChecker->countExplicitIcons(),
            'isForm' => $this->accessibilityChecker->isForm(),
            'missingAssociatedLabelForInput' => $this->accessibilityChecker->getListMissingForLabelsInForm(),
            'countMissingTextInButtons' => $this->accessibilityChecker->countNonExplicitButtons(),
            'brokenLinks' => $this->brokenLinkChecker->getExternalBrokenLinks(),
        ];

        if (isset($event)) {
            $event->stop();
        }
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getBrokenLinks(): ?array
    {
        return $this->data['brokenLinks'];
    }

    public function isForm(): bool
    {
        return $this->data['isForm'];
    }

    public function getCountMissingTextInButtons(): int
    {
        return $this->data['countMissingTextInButtons'];
    }

    public function getMissingAssociatedLabelForInput(): array
    {
        return $this->data['missingAssociatedLabelForInput'];
    }

    public function listMissingAltFromImages(): array
    {
        return $this->data['listMissingAltFromImages'];
    }

    public function listNonExplicitIcons(): array
    {
        return $this->data['listNonExplicitIcons'];
    }

    public function getCountAllIcons(): int
    {
        return $this->data['countAllIcons'];
    }

    public function getCountAllExplicitIcons(): int
    {
        return $this->data['countAllExplicitIcons'];
    }

    public function getTitle(): ?string
    {
        return $this->data['title'];
    }

    public function getH1(): ?string
    {
        return $this->data['h1'];
    }

    public function getCountAllImages(): int
    {
        return $this->data['countAllImages'];
    }

    public function getCountAltFromImages(): int
    {
        return $this->data['countAltFromImages'];
    }
}
