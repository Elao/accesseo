<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Tests\AccessibilityChecker;

use Elao\Bundle\Accesseo\Checker\AccessibilityChecker;
use Elao\Bundle\Accesseo\Checker\Headline;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class AccessibilityCheckerTest extends TestCase
{
    public function testCountHeadlines(): void
    {
        $optimizationChecker = $this->getAccessibilityChecker('headlines-1-6.html');

        $headlines = [
            'h1' => 2,
            'h2' => 3,
            'h3' => 4,
            'h4' => 1,
            'h5' => 1,
            'h6' => 1,
        ];

        static::assertEquals($headlines, $optimizationChecker->countHeadlinesByHn());
    }

    public function testGetHeadlines(): void
    {
        $optimizationChecker = $this->getAccessibilityChecker('headlines-1-6.html');

        $h1Headline = new Headline(1, 'This is one h1');
        $firstHeadline = new Headline(1, 'This is h1');
        $firstHeadline->addChild(new Headline(2, 'This is h2'));
        $secondHeadlineOfFirst = new Headline(2, 'Another h2');
        $firstHeadline->addChild($secondHeadlineOfFirst);
        $secondHeadlineOfFirst->addChild(new Headline(3, 'And a h3'));
        $secondHeadlineOfFirst->addChild(new Headline(3, 'Another h3'));

        $thirdHeadlineOfFirst = new Headline(2, 'And so another h2');
        $thirdHeadlineOfFirst->addChild(new Headline(3, 'And a h3'));

        $h3Somewhere = new Headline(3, 'Another h3');

        $nextHeadline = new Headline(4, 'And now a H4');
        $nextNextHeadline = new Headline(5, 'And now a H5');
        $nextNextHeadline->addChild(new Headline(6, 'And now a H6'));
        $nextHeadline->addChild($nextNextHeadline);

        $h3Somewhere->addChild($nextHeadline);

        $thirdHeadlineOfFirst->addChild($h3Somewhere);
        $firstHeadline->addChild($thirdHeadlineOfFirst);

        $treeExpected = [
            $h1Headline,
            $firstHeadline,
        ];

        static::assertEquals(\count($treeExpected), \count($optimizationChecker->getHeadlineTree()));
        static::assertEquals($treeExpected, $optimizationChecker->getHeadlineTree());
    }

    public function testSemanticalSections(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('sections.html');

        static::assertEquals(true, $accessibilityChecker->isHeader());
        static::assertEquals(true, $accessibilityChecker->isFooter());
        static::assertEquals(true, $accessibilityChecker->isArticle());
        static::assertEquals(true, $accessibilityChecker->isAside());
        static::assertEquals(true, $accessibilityChecker->isNavInHeader());
        static::assertEquals(true, $accessibilityChecker->isHeaderInArticle());
    }

    public function testNoSemanticalSections(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('no-sections.html');

        static::assertEquals(false, $accessibilityChecker->isHeader());
        static::assertEquals(true, $accessibilityChecker->isFooter());
        static::assertEquals(false, $accessibilityChecker->isArticle());
        static::assertEquals(true, $accessibilityChecker->isAside());
        static::assertEquals(false, $accessibilityChecker->isNavInHeader());
        static::assertEquals(false, $accessibilityChecker->isHeaderInArticle());
    }

    public function testNoExplicitsButtons(): void
    {
        $accessibility = $this->getAccessibilityChecker('index.html');
        static::assertEquals(2, $accessibility->countNonExplicitButtons());
    }

    public function testIsThereAForm(): void
    {
        $form = $this->getAccessibilityChecker('form-missing-for.html');
        $alsoForm = $this->getAccessibilityChecker('form-missing-for.html');
        $noForm = $this->getAccessibilityChecker('index.html');

        static::assertEquals(true, $form->isForm());
        static::assertEquals(true, $alsoForm->isForm());
        static::assertEquals(false, $noForm->isForm());
    }

    public function testMissingForInForm(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('form-missing-for.html');
        static::assertEquals(['name', 'email'], $accessibilityChecker->getListMissingForLabelsInForm());
    }

    public function testNoMissingForInForm(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('form-with-for.html');
        static::assertEquals([2 => ''], $accessibilityChecker->getListMissingForLabelsInForm());
    }

    public function testNoForm(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('headlines-1-6.html');
        static::assertEquals([], $accessibilityChecker->getListMissingForLabelsInForm());
    }

    public function testIsForm(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('form-with-for.html');
        static::assertEquals(true, $accessibilityChecker->isForm());
    }

    public function testGetLinks(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('with-links.html');

        $links = [
            'http://www.google.com',
            'https://www.nasa.gov/',
            'http://www.google.com/error',
            'http://www.google/error',
            'http://www.google/timeout',
            ];
        static::assertEquals($links, $accessibilityChecker->getLinks());
    }

    public function testGetNavElements(): void
    {
        $accessibilityChecker = $this->getAccessibilityChecker('nav-case1.html');

        $expected =
            [
                [
                    'id' => 'main-nav1',
                    'ariaLabel' => 'Main1',
                ],
                [
                    'id' => 'main-nav2',
                    'ariaLabel' => 'Main2',
                ],
              [
                  'id' => 'main-nav0',
                  'ariaLabel' => 'Main0',
              ],
                [
                    'id' => 'footer-nav',
                    'ariaLabel' => 'Footer',
                ],
                [
                    'id' => 'footer-bis-nav',
                    'ariaLabel' => '',
                ],
            ];

        static::assertEquals($expected, $accessibilityChecker->getNavigationElements());
    }

    public function getAccessibilityChecker($filename): AccessibilityChecker
    {
        $html = file_get_contents(sprintf(__DIR__.'/../AccessibilityChecker/%s', $filename));
        $crawler = new Crawler($html);

        return new AccessibilityChecker($crawler);
    }
}
