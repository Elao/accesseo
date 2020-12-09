<?php

declare(strict_types=1);

namespace Elao\Bundle\SeoTool\Checker;

use Symfony\Component\DomCrawler\Crawler;

class AccessibilityChecker
{
    const MAX_LEVEL_HEADLINES = 6;

    /** @var Crawler */
    private $crawler;

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function countHeadlinesByHn(): array
    {
        $i = 1;
        $headings = [];
        while ($i <= 6):
            try {
                $headings[sprintf('h%d', $i)] = $this->crawler->filter(sprintf('h%d', $i))->count();
            } catch (\Exception $e) {
            }
        ++$i;
        endwhile;

        return $headings;
    }

    public function getHeadlineTree(): array
    {
        $treeHeadlines = [];

        /** @var \IteratorIterator $headlines */
        $headlines = $this->crawler->filter('h1, h2, h3, h4, h5, h6');

        $current = null;
        $previous = null;

        foreach ($headlines as $element) {
            /* @var \DOMElement $element */
            $level = (int) $element->tagName[1];
            $current = new Headline($level, $element->textContent);
            $parent = $previous !== null ? $previous->getParentForLevel($level) : null;

            if ($parent !== null) {
                $parent->addChild($current);
            } else {
                $treeHeadlines[] = $current;
            }

            $previous = $current;
        }

        return $treeHeadlines;
    }

    public function isHeader(): bool
    {
        return \count($this->crawler->filter('body > header')) >= 1;
    }

    public function isAside(): bool
    {
        return \count($this->crawler->filter('aside')) >= 1;
    }

    public function isNavInHeader(): bool
    {
        return \count($this->crawler->filter('header > nav')) >= 1;
    }

    public function isFooter(): bool
    {
        return \count($this->crawler->filter('body > footer')) >= 1;
    }

    public function isArticle(): bool
    {
        return \count($this->crawler->filter('article')) >= 1;
    }

    public function isHeaderInArticle(): bool
    {
        return \count($this->crawler->filter('article > header')) >= 1;
    }

    public function countNonExplicitButtons(): int
    {
        $buttons = $this->crawler->filter('a');
        $buttonsWithNoContent = [];

        foreach ($buttons as $element) {
            /* @var \DOMElement $element */
            if ('' === trim($element->textContent)) {
                $buttonsWithNoContent[] = $element;
            }
        }

        return \count($buttonsWithNoContent);
    }

    public function isForm(): bool
    {
        return \count($this->crawler->filter('form')) > 0;
    }

    public function getListMissingForLabelsInForm(): array
    {
        $for = $this->crawler->filter('label')->extract(['for']);
        $inputsName = $this->crawler->filter('input')->extract(['name']);

        return array_diff($inputsName, $for);
    }
}
