<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GGG\Bundle\BpmnBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Peter Tilsen <peter.tilsen@glassesgroupglobal.my>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ggg_bpmn');

        $rootNode
            ->children()
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        #$this->addValidationGroupsSection($rootNode);
        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `validation_groups` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('api_client')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('ggg'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `classes` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->isRequired()
                    ->children()
                        ->arrayNode('bpmn')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('GGG\Bundle\BpmnBundle\Model\Bpmn')->end()
                                ->scalarNode('controller')->defaultValue('GGG\Bundle\BpmnBundle\Controller\BpmnController')->end()
                                ->scalarNode('repository')->defaultValue('GGG\Bundle\BpmnBundle\Doctrine\ORM\Bpmn')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
