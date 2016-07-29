<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\DependencyInjection;

use Mcfedr\BeanstalkQueueDriverBundle\Command\BeanstalkCommand;
use Mcfedr\BeanstalkQueueDriverBundle\Manager\BeanstalkQueueManager;
use Pheanstalk\Connection;
use Pheanstalk\PheanstalkInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class McfedrBeanstalkQueueDriverExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
    }

    public function prepend(ContainerBuilder $container)
    {
        // get all Bundles
        $bundles = $container->getParameter('kernel.bundles');
        // determine if AcmeGoodbyeBundle is registered
        if (isset($bundles['McfedrQueueManagerBundle'])) {
            $container->prependExtensionConfig('mcfedr_queue_manager', [
                'drivers' => [
                    'beanstalkd' => [
                        'class' => BeanstalkQueueManager::class,
                        'options' => [
                            'host' => '127.0.0.1',
                            'port' => PheanstalkInterface::DEFAULT_PORT,
                            'default_queue' => PheanstalkInterface::DEFAULT_TUBE,
                            'connection' => [
                                'timeout' => Connection::DEFAULT_CONNECT_TIMEOUT,
                                'persistent' => false
                            ]
                        ],
                        'command_class' => BeanstalkCommand::class
                    ]
                ]
            ]);
        }
    }
}
