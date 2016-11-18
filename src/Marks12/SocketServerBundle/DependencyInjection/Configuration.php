<?php

namespace Marks12\SocketServerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('marks12_socket_server');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->variableNode('class')
                    ->defaultValue('Marks12\SocketServerBundle\Server\ResponseFactoryExample')
                ->end()
                ->variableNode('address')
                    ->defaultValue('0.0.0.0')
                ->end()
                ->variableNode('port')
                    ->defaultValue('10000')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
