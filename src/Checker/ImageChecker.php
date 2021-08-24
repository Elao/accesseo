<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;

class ImageChecker
{
    /** @var Crawler */
    private $crawler;

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
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
            ->filter('i');

        return \count($images);
    }

    public function countExplicitIcons(): int
    {
        $images = $this->crawler
            ->filter('i')
            ->extract(['aria-hidden']);

        $images = array_filter($images);

        return \count($images);
    }

    public function listNonExplicitIcons(): array
    {
        $countIcons = $this->countIcons();
        $icons = [];

        $i = 0;
        while ($i < $countIcons) {
            $icons[$i] = [
                'class' => $this->crawler->filter('i')->eq($i)->attr('class'),
                'aria-hidden' => '',
                'html' => $this->crawler->filter('i')->eq($i)->outerHtml(),
            ];

            try {
                $icons[$i]['aria-hidden'] = $this->crawler->filter('i')->eq($i)->attr('aria-hidden');
            } catch (\Exception $e) {
            }

            ++$i;
        }

        $missingAriaHidden = [];

        foreach ($icons as $icon) {
            if (null === $icon['aria-hidden']) {
                $missingAriaHidden[] = ['class' => $icon['class'], 'html' => $icon['html']];
            }
        }

        return $missingAriaHidden;
    }
}
