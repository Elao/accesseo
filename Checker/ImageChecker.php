<?php

declare(strict_types=1);

namespace Elao\Bundle\SEOTool\Checker;

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

    public function countAllImages(): int
    {
        $images = $this->crawler
            ->filter('img');

        return \count($images);
    }

    public function listImagesWhithoutAlt(): array
    {
        $countImages = $this->countAllImages();
        $images = [];

        $i = 0;
        while ($i < $countImages) {
            $images[$i] = [
                'src' => $this->crawler->filter('img')->eq($i)->attr('src'),
                'alt' => '',
            ];

            try {
                $images[$i]['alt'] = $this->crawler->filter('img')->eq($i)->attr('alt');
            } catch (\Exception $e) {
            }

            ++$i;
        }

        $missingAlt = [];

        foreach ($images as $image) {
            if ('' === $image['alt']) {
                $missingAlt[] = $image['src'];
            }
        }

        return $missingAlt;
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
