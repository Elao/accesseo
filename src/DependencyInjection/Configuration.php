<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('elao_accesseo');
        $rootNode = method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('elao_accesseo');
        $rootNode
            ->canBeDisabled()
            ->children()
                ->booleanNode('enabled_seo_panel')
                    ->defaultTrue()
                ->end()
                ->booleanNode('enabled_accessibility_panel')
                    ->defaultTrue()
                ->end()
                ->arrayNode('icons')
                    ->defaultValue(['icon'])
                    ->scalarPrototype()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
