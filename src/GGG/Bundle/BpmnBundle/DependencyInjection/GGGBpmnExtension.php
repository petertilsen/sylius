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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * GGG Bpmn Extension
 *
 * @author Peter Tilsen <peter.tilsen@glassesgroupglobal.my>
 */
class GGGBpmnExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        #$loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        #$loader->load('services.xml');
    }



    /**
     * {@inheritdoc}
     *
     * @throws ServiceNotFoundException
     */
    public function prepend(ContainerBuilder $container)
    {
        return;

        if (!$container->hasExtension('fos_oauth_server')) {
            throw new ServiceNotFoundException('FOSOAuthServerBundle must be registered in kernel.');
        }

        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $container->prependExtensionConfig('fos_oauth_server', array(
                'db_driver'           => 'orm',
                'client_class'        => $config['classes']['api_client']['model'],
                'access_token_class'  => $config['classes']['api_access_token']['model'],
                'refresh_token_class' => $config['classes']['api_refresh_token']['model'],
                'auth_code_class'     => $config['classes']['api_auth_code']['model'],

                'service'             => array(
                    'user_provider' => 'fos_user.user_provider'
                ),
            ));
    }
}
