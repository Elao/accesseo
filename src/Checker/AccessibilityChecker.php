<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;

class AccessibilityChecker
{
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
        while ($i <= 6) {
            try {
                $headings[sprintf('h%d', $i)] = $this->crawler->filter(sprintf('h%d', $i))->count();
            } catch (\Exception $e) {
            }
            ++$i;
        }

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

    /**
     * Returns an array of all inputs for the form, with associated of missing label
     */
    public function getListMissingForLabelsInForm(): array
    {
        $formRows = [];

        $rawLabels = $this->crawler->filter('label')->extract(['for', '_text']);
        $inputsName = $this->crawler->filter('input')->extract(['type', 'name']);

        $labels = [];

        foreach ($rawLabels as $label) {
            if ($label[0] == !'') {
                $labels[$label[0]] = $label[1];
            }
        }

        foreach ($inputsName as $input) {
            $label = \array_key_exists($input[1], $labels) ? $labels[$input[1]] : '';
            $formRows[] =
                        [
                            'type' => $input[0],
                            'name' => $input[1],
                            'label' => $label,
                        ];
        }

        return $formRows;
    }

    public function getLinks(): ?array
    {
        $links = [];

        $here = $this->crawler->filter('a')->links();

        foreach ($here as $link) {
            if (0 === strpos($link->getUri(), 'http')) {
                $links[] = $link->getUri();
            }
        }

        return $links;
    }

    public function getNavigationElements(): ?array
    {
        $roles = $this->crawler->filter('[role="navigation"], nav');
        $rolesData = $roles->extract(['id', 'aria-label']);
        $roles->each(function ($node, $i) use (&$rolesData): void {
            $rolesData[$i]['id'] = $rolesData[$i][0];
            $rolesData[$i]['ariaLabel'] = $rolesData[$i][1];
            $rolesData[$i]['html'] = $node->ancestors->outerHtml;
        });

        return $rolesData;
    }
}
