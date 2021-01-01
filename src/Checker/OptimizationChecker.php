<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Symfony\Component\DomCrawler\Crawler;

class OptimizationChecker
{
    /** @var Crawler */
    private $crawler;

    const TWITTER_PROPERTIES = ['card', 'title', 'description', 'site', 'creator', 'image'];

    const OG_PROPERTIES = ['title', 'locale', 'description', 'url', 'site_name', 'image'];

    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    public function getTitle(): ?string
    {
        try {
            $title = $this->crawler->filter('head > title')->text();

            if ($title === '') {
                return null;
            }

            return $title;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getMetaDescription(): ?string
    {
        try {
            $metaDescription = $this->crawler->filter('head > meta[name="description"]')->first()->attr('content');

            if ($metaDescription === '') {
                return null;
            }

            return $metaDescription;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getH1(): ?string
    {
        try {
            $h1 = $this->crawler->filter('h1')->first()->text();
        } catch (\Exception $e) {
            return null;
        }

        if ($h1 === '') {
            return null;
        }

        return $h1;
    }

    public function atLeastOneH1(): bool
    {
        try {
            $elements = $this->crawler->filter('h1');

            return 1 <= \count($elements);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTwitterPropertiesLevel(): ?string
    {
        $twitterProperties = $this->getProperties('twitter', self::TWITTER_PROPERTIES);

        if (0 === \count($twitterProperties)) {
            return 'missing';
        }

        return \count($twitterProperties) == \count(self::TWITTER_PROPERTIES) ? 'completed' : 'almost-completed';
    }

    public function getTwitterProperties(): array
    {
        return $twitterProperties = $this->getProperties('twitter', self::TWITTER_PROPERTIES);
    }

    public function getOpenGraphProperties(): array
    {
        return $this->getProperties('og', self::OG_PROPERTIES);
    }

    public function getOpenGraphLevel(): ?string
    {
        $openGraphProperties = $this->getProperties('og', self::OG_PROPERTIES);

        if (0 === \count($openGraphProperties)) {
            return 'missing';
        }

        return \count($openGraphProperties) == \count(self::OG_PROPERTIES) ? 'completed' : 'almost-completed';
    }

    public function getMissingTwitterProperties(): array
    {
        return $this->getMissingPropertiesByType('twitter', self::TWITTER_PROPERTIES);
    }

    public function getMissingOGProperties(): array
    {
        return $this->getMissingPropertiesByType('og', self::OG_PROPERTIES);
    }

    public function getMissingPropertiesByType(string $type, array $properties): array
    {
        $propertiesCompleted = $this->getProperties($type, $properties);

        if (0 < \count($propertiesCompleted)) {
            $allProperties = $properties;
            $missing = [];

            foreach ($allProperties as $property) {
                if (!\array_key_exists($property, $propertiesCompleted)) {
                    $missing[] = $property;
                }
            }

            return $missing;
        }

        return $properties;
    }

    public function getProperty(string $property): ?string
    {
        $meta = $this->crawler->filter(sprintf('head > meta[property="%s"]', $property))->eq(0)->attr('content');

        if ($meta === '') {
            return null;
        }

        return $meta;
    }

    public function getProperties(string $type, array $properties): array
    {
        $propertiesCompleted = [];

        foreach ($properties as $property) {
            try {
                $propertiesCompleted[$property] = $this->getProperty(sprintf('%s:%s', $type, $property));
            } catch (\Exception $e) {
                $propertiesCompleted[$property] = null;
            }
        }

        return array_filter($propertiesCompleted);
    }

    public function isHreflang(): bool
    {
        return \count($this->getHreflang()) >= 1;
    }

    public function getHreflang(): array
    {
        $hreflang = [];

        $links = $this->crawler->filter('link')->extract(['rel', 'hreflang', 'href']);

        foreach ($links as $link) {
            if ($link[0] === 'alternate') {
                $hreflang[] = [
                    'hreflang' => $link[1],
                    'href' => $link[2],
                ];
            }
        }

        return $hreflang;
    }
}
