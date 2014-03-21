<?php

namespace mcfedr\Queue\QueueManagerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class mcfedrQueueDriverPheanstalkExtension extends Extension
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
        if (!isset($bundles['mcfedrQueueManagerBundle'])) {
            $container->prependExtensionConfig('mcfedr_queue_manager', [
                'drivers' => [
                    'beanstalkd' => 'mcfedr\Queue\Driver\PheanstalkBundle\Manager\PheanstalkQueueManager'
                ]
            ]);
        }
    }
}
