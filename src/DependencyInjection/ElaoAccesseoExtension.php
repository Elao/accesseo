<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\DependencyInjection;

use Elao\Bundle\Accesseo\DataCollector\AccessibilityCollector;
use Elao\Bundle\Accesseo\DataCollector\SeoCollector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class ElaoAccesseoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config/')
        );

        if ($config['enabled']) {
            $loader->load('services.xml');

            if (!$config['enabled_seo_panel']) {
                $definition = $container->getDefinition('Elao\Bundle\Accesseo\DataCollector\SeoCollector');
                $definition->clearTags();
            }

            if (!$config['enabled_accessibility_panel']) {
                $definition = $container->getDefinition('Elao\Bundle\Accesseo\DataCollector\AccessibilityCollector');
                $definition->clearTags();
            }

            $container->getDefinition(AccessibilityCollector::class)->replaceArgument('$icons', $config['icons']);
            $container->getDefinition(SeoCollector::class)->replaceArgument('$icons', $config['icons']);
        }
    }
}
