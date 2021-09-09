<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;

class ImageChecker
{
    /** @var Crawler */
    private $crawler;

    /** @var array */
    private $icons;

    public function __construct(Crawler $crawler, array $icons)
    {
        $this->crawler = $crawler;
        $this->icons = $icons;
    }

    public function setIcons(array $value): void
    {
        $this->icons = $value;
    }

    public function countAltFromImages(): int
    {
        $alt = $this->crawler
            ->filter('img')
            ->extract(['alt']);

        $alt = array_filter($alt);

        return \count($alt);
    }

    public function listImagesUrlAndAlt(): array
    {
        $images = $this->crawler->filter('img');
        $extract = $images->extract(['src', 'alt']);

        $data = [];

        $images->each(function ($node, $i) use ($extract, &$data): void {
            $data[$i]['src'] = $extract[$i][0];
            $data[$i]['alt'] = $extract[$i][1];
            $data[$i]['html'] = $node->outerHtml();
        });

        return $data;
    }

    public function listImagesUrlAndAltTooLong(): array
    {
        $allImages = $this->listImagesUrlAndAlt();
        $altTooLong = [];
        foreach ($allImages as $image) {
            if (\strlen($image['alt']) > 80) {
                $altTooLong[] = [
                    'img' => $image['html'],
                    'alt' => $image['alt'],
                ];
            }
        }

        return $altTooLong;
    }

    public function countAllImages(): int
    {
        return $this->crawler->filter('img')->count();
    }

    /**
     * @return array
     *               Returns two arrays :
     *               - Missing Alt
     *               - Empty Alt
     */
    public function listImagesWithoutAlt(): array
    {
        $images = $this->listImagesUrlAndAlt();

        $missingAlt = [];
        $emptyAlt = [];

        foreach ($images as $img) {
            if (strpos($img['html'], 'alt="')) {
                if ('' === $img['alt']) {
                    $emptyAlt[] = $img['html'];
                }
            } else {
                $missingAlt[] = $img['html'];
            }
        }

        return ['missingAlt' => $missingAlt, 'emptyAlt' => $emptyAlt];
    }

    public function countIcons(): int
    {
        $images = $this->crawler
            ->filter(implode(',', array_map(function ($i) { return sprintf('.%s', $i); }, $this->icons)));

        return \count($images);
    }

    public function countExplicitIcons(): int
    {
        $images = $this->crawler
            ->filter(implode(',', array_map(function ($i) { return sprintf('.%s', $i); }, $this->icons)))
            ->extract(['aria-hidden']);

        $images = array_filter($images);

        return \count($images);
    }

    public function listNonExplicitIcons(): array
    {
        $missingAriaHidden = [];

        $iconsElements = $this->crawler
            ->filter(implode(',', array_map(function ($i) { return sprintf('.%s', $i); }, $this->icons)))
        ;

        $iconsElements->each(
            function ($icon) use (&$missingAriaHidden): void {
                if ($icon->attr('aria-hidden') === null || $icon->attr('aria-hidden') === '') {
                    $missingAriaHidden[] = [
                        'class' => $icon->attr('class'),
                        'html' => $icon->outerHtml(),
                    ];
                }
            }
        );

        return $missingAriaHidden;
    }
}
