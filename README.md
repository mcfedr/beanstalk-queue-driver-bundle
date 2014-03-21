# Pheanstalk Queue Driver Bundle

A driver for [Queue Manager Bundle](https://github.com/mcfedr/queue-manager-bundle) that uses beanstalkd

[![Latest Stable Version](https://poser.pugx.org/mcfedr/queue-driver-pheanstalk-bundle/v/stable.png)](https://packagist.org/packages/mcfedr/queue-driver-pheanstalk-bundle)
[![License](https://poser.pugx.org/mcfedr/queue-driver-pheanstalk-bundle/license.png)](https://packagist.org/packages/mcfedr/queue-driver-pheanstalk-bundle)

## Install

### Composer

    php composer.phar require mcfedr/queue-driver-pheanstalk-bundle

### AppKernel

Include the bundle in your AppKernel

    public function registerBundles()
    {
        $bundles = array(
            ...
            new mcfedr\Queue\Driver\PheanstalkBundle\mcfedrQueueDriverPheanstalkBundle(),

## Config

With this bundle installed you can setup your queue manager config similar to this:

    mcfedr_queue_manager:
        managers:
            default:
                driver: beanstalkd
                options:
                    host: 127.0.0.1
                    port: 11300
                    default_queue: mcfedr_queue
