<?php
namespace FlexModel\FlexModelBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration validates and merges configuration from the app/config files.
 *
 * @author Matthijs Hasenpflug <matthijs@connectholland.nl>
 */

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('file_upload');
        $rootNode
            ->children()
                ->scalarNode('path')->end()
            ->end();
        return $treeBuilder;
    }
}