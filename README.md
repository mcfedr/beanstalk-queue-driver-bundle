# Queue Bundle

A bundle for accessing job queues

[![Latest Stable Version](https://poser.pugx.org/mcfedr/queuebundle/v/stable.png)](https://packagist.org/packages/mcfedr/queuebundle)
[![License](https://poser.pugx.org/mcfedr/queuebundle/license.png)](https://packagist.org/packages/mcfedr/queuebundle)

## Install

### Composer

    php composer.phar require mcfedr/queuebundle

### AppKernel

Include the bundle in your AppKernel

    public function registerBundles()
    {
        $bundles = array(
            ...
            new mcfedr\QueueBundle\mcfedrQueueBundle(),

## Config

This is the full default configuration, you can override these values, and also add your own managers

    mcfedr_queue:
        managers:
            default:
                driver: beanstalkd
                host: 127.0.0.1
                port: 11300
                default_queue: mcfedr_queue


## Usage

Each manager will be a service you can access with the name `"mcfedr_queue.$name"`.
It implements the `QueueManager` interface, where you can call just 3 simple methods.
