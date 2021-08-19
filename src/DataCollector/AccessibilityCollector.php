<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\DataCollector;

use Elao\Bundle\Accesseo\Checker\AccessibilityChecker;
use Elao\Bundle\Accesseo\Checker\ImageChecker;
use Elao\Bundle\Accesseo\Checker\OptimizationChecker;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\VarDumper\Cloner\Data;

class AccessibilityCollector extends DataCollector
{
    /** @var ImageChecker */
    public $imageChecker;

    /** @var AccessibilityChecker */
    public $accessibilityChecker;

    /** @var OptimizationChecker */
    public $optimizationChecker;

    /** @var Stopwatch|null */
    private $stopwatch;

    public function __construct(?Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    public function getName(): string
    {
        return 'elao.accesseo.accessibility_collector';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        if ($this->stopwatch) {
            $event = $this->stopwatch->start('accessibility-collect', 'accesseo');
        }

        $crawler = new Crawler((string) $response->getContent(), $request->getUri(), $request->getBaseUrl());

        $this->imageChecker = new ImageChecker($crawler);
        $this->accessibilityChecker = new AccessibilityChecker($crawler);

        $this->optimizationChecker = new OptimizationChecker($crawler);

        $this->data = [
            'response' => $response,
            'title' => $this->optimizationChecker->getTitle(),
            'metaDescription' => $this->optimizationChecker->getMetaDescription(),
            'h1' => $this->optimizationChecker->getH1(),
            'countAllImages' => $this->imageChecker->countAllImages(),
            'countAltFromImages' => $this->imageChecker->countAltFromImages(),
            'listImgUrlAndAlt' => $this->imageChecker->listImagesUrlAndAlt(),
            'listMissingAltFromImages' => $this->imageChecker->listImagesWithoutAlt(),
            'listImagesUrlAndAltTooLong' => $this->imageChecker->listImagesUrlAndAltTooLong(),
            'listNonExplicitIcons' => $this->imageChecker->listNonExplicitIcons(),
            'countAllIcons' => $this->imageChecker->countIcons(),
            'countAllExplicitIcons' => $this->imageChecker->countExplicitIcons(),
            'isForm' => $this->accessibilityChecker->isForm(),
            'missingAssociatedLabelForInput' => $this->accessibilityChecker->getListMissingForLabelsInForm(),
            'countMissingTextInButtons' => $this->accessibilityChecker->countNonExplicitButtons(),
            'links' => $this->accessibilityChecker->getLinks(),
            'navigationElements' => $this->accessibilityChecker->getNavigationElements(),
            'headlinesTree' => $this->accessibilityChecker->getHeadlineTree(),
        ];

        $this->data = $this->cloneVar($this->data);

        if (isset($event)) {
            $event->stop();
        }
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function seek(string $key): Data
    {
        return $this->data->seek($key);
    }

    public function isForm(): bool
    {
        return $this->data['isForm'];
    }

    public function listImagesWithoutAlt(): array
    {
        return $this->data['listImagesWithoutAlt'];
    }

    public function getCountMissingTextInButtons(): int
    {
        return $this->data['countMissingTextInButtons'];
    }

    public function getMissingAssociatedLabelForInput(): array
    {
        return $this->data['missingAssociatedLabelForInput']->getValue();
    }

    public function getNavigationElements(): array
    {
        return $this->data['navigationElements']->getValue();
    }

    public function getLinks(): array
    {
        return $this->data['links']->getValue();
    }

    public function getListImgUrlAndAlt(): array
    {
        return $this->data['listImgUrlAndAlt']->getValue();
    }

    public function getListImagesUrlAndAltTooLong(): array
    {
        return $this->data['listImagesUrlAndAltTooLong']->getValue();
    }

    public function listMissingAltFromImages(): array
    {
        return $this->data['listMissingAltFromImages']->getValue();
    }

    public function listNonExplicitIcons(): array
    {
        return $this->data['listNonExplicitIcons']->getValue();
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

    public function getHeadlinesTree(): array
    {
        return $this->data['headlinesTree']->getValue();
    }

    public function getMetaDescription(): ?string
    {
        return $this->data['metaDescription'];
    }
}
