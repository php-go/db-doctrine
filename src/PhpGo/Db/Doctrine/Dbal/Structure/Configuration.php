<?php
/**
 * User: dongww
 * Date: 2014-9-25
 * Time: 20:35
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('structure');

        $rootNode
            ->children()
                ->arrayNode('tables')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('fields')
                                ->prototype('array')
                                    ->children()
                                        ->enumNode('type')
                                            ->isRequired()
                                            ->values(['string', 'integer', 'float', 'text', 'datetime', 'date', 'time', 'boolean', 'array'])
                                        ->end()
                                        ->booleanNode('required')
                                            ->defaultValue(false)
                                        ->end()
                                        ->booleanNode('unique')
                                            ->defaultValue(false)
                                        ->end()
                                        ->booleanNode('index')
                                            ->defaultValue(false)
                                        ->end()
                                        ->integerNode('length')
                                            ->defaultValue(null)
                                        ->end()
                                        ->variableNode('default')
                                            ->defaultValue(null)
                                        ->end()
                                        ->booleanNode('autoincrement')
                                            ->defaultValue(null)
                                        ->end()
                                        ->booleanNode('fixed')
                                            ->defaultValue(null)
                                        ->end()
                                        ->integerNode('precision')
                                            ->defaultValue(null)
                                        ->end()
                                        ->integerNode('scale')
                                            ->defaultValue(null)
                                        ->end()
                                        ->booleanNode('unsigned')
                                            ->defaultValue(null)
                                        ->end()
                                        ->variableNode('comment')
                                            ->defaultValue(null)
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('belong_to')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('extensions')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('many_many')
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
