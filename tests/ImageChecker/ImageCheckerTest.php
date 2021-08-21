<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\ImageChecker;

use Elao\Bundle\Accesseo\Checker\ImageChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class ImageCheckerTest extends TestCase
{
    public function testCountImages(): void
    {
        $imgChecker = $this->getImageChecker('images.html');

        static::assertEquals(2, $imgChecker->countAllImages());
    }

    public function testCountImagesWithAltTest(): void
    {
        $imgChecker = $this->getImageChecker('images.html');

        static::assertEquals(1, $imgChecker->countAltFromImages());
    }

    public function testCountZeroImage(): void
    {
        $imgChecker = $this->getImageChecker('no-images.html');

        static::assertEquals(0, $imgChecker->countAllImages());
    }

    public function testCountZeroImageWithAltTest(): void
    {
        $imgChecker = $this->getImageChecker('no-images.html');

        static::assertEquals(0, $imgChecker->countAltFromImages());
    }

    public function testExplicitIcons(): void
    {
        $imgChecker = $this->getImageChecker('explicit-icons.html');
        static::assertEquals(1, $imgChecker->countExplicitIcons());
    }

    public function testIcons(): void
    {
        $imgChecker = $this->getImageChecker('explicit-icons.html');
        static::assertEquals(2, $imgChecker->countIcons());
    }

    public function testListImagesWithoutAlt(): void
    {
        $imgChecker = $this->getImageChecker('images.html');
        static::assertEquals(['missingAlt' => [], 'emptyAlt' => ['<img src="https://image.fr/image1.jpg" alt="">']], $imgChecker->listImagesWithoutAlt());
    }

    public function testListImagesWithoutAriaHidden(): void
    {
        $imgChecker = $this->getImageChecker('explicit-icons.html');
        static::assertEquals(['icon icon--alert'], $imgChecker->listNonExplicitIcons());
    }

    public function testListImagesUrlAndAlt(): void
    {
        $imgChecker = $this->getImageChecker('images.html');
        $expected = [
            ['src' => 'https://image.fr/image1.jpg', 'alt' => '', 'html' => '<img src="https://image.fr/image1.jpg" alt="">'],
            ['src' => 'https://image.fr/image2.jpg', 'alt' => 'Un texte', 'html' => '<img src="https://image.fr/image2.jpg" alt="Un texte">'],
        ];
        static::assertEquals($expected, $imgChecker->listImagesUrlAndAlt());
    }

    public function testListImagesUrlAndAltNoData(): void
    {
        $imgChecker = $this->getImageChecker('no-images.html');
        static::assertEquals([], $imgChecker->listImagesUrlAndAlt());
    }

    public function getImageChecker($filename): ImageChecker
    {
        $html = file_get_contents(sprintf(__DIR__.'/../ImageChecker/%s', $filename));
        $crawler = new Crawler($html);

        return new ImageChecker($crawler);
    }
}
