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
        return $this->crawler
            ->filter('img')
            ->extract(['src', 'alt']);
    }

    public function listImagesUrlAndAltTooLong(): array
    {
        $allImages = $this->listImagesUrlAndAlt();
        $altTooLong = [];

        foreach ($allImages as $image) {
            if (\strlen($image[1]) > 80) {
                $altTooLong[] = [
                    'img' => $image[0],
                    'alt' => $image[1],
                ];
            }
        }

        return $altTooLong;
    }

    public function countAllImages(): int
    {
        return $this->crawler->filter('img')->count();
    }

    public function listImagesWithoutAlt(): array
    {
        $images = $this->crawler->filter('img')->extract(['src', 'alt']);
        $missingAlt = array_filter($images, function ($img) { return '' === $img[1]; });

        return array_values(array_map(function ($img) { return $img[0]; }, $missingAlt));
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
                $missingAriaHidden[] = $icon['class'];
            }
        }

        return $missingAriaHidden;
    }
}
