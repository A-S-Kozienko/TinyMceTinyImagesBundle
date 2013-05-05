<?php

namespace ASK\TinyMceTinyImagesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ask_tiny_mce_tiny_images');

        $rootNode
            ->children()
                ->scalarNode('image_field')->defaultValue('file')->end()
                ->scalarNode('thumbnail_filter')->defaultValue('thumbnail')->end()
                ->scalarNode('image_class')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
