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
    public function getListLabelsInForm(): array
    {
        $formRows = [];

        $rawLabels = $this->crawler->filter('label')->extract(['for', '_text']);

        $inputs = $this->crawler->filter('input, select, textarea');
        $data = $inputs->extract(['type', 'id']);
        $inputs->each(function ($node, $i) use (&$data): void { $data[$i]['html'] = $node->outerHtml(); });

        $labels = [];

        foreach ($rawLabels as $label) {
            if ($label[0] == !'') {
                $labels[$label[0]] = $label[1];
            }
        }

        foreach ($data as $input) {
            if ($input[0] !== 'submit') {
                $label = \array_key_exists($input[1], $labels) ? $labels[$input[1]] : '';
                $formRows[] =
                    [
                        'type' => $input[0],
                        'name' => $input[1],
                        'html' => $input['html'],
                        'label' => $label,
                    ];
            }
        }

        return $formRows;
    }

    public function countMissingLabelsInForms(): int
    {
        $list = $this->getListLabelsInForm();
        $missing = 0;

        foreach ($list as $elt) {
            if ($elt['label'] === '') {
                $missing = $missing + 1;
            }
        }

        return $missing;
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
        $crawler = $this->crawler->filter('[role="navigation"], nav');

        return $crawler->each(function (Crawler $element) {
            return [
                'tag' => str_replace($element->html(), '', $element->outerHtml()),
                'ariaLabel' => $element->first()->attr('aria-label'),
                'links' => $element->filter('a')->extract(['href']),
            ];
        });
    }
}
