# Beanstalk Queue Driver Bundle

A driver for [Queue Manager Bundle](https://github.com/mcfedr/queue-manager-bundle) that uses beanstalkd

[![Latest Stable Version](https://poser.pugx.org/mcfedr/beanstalk-queue-driver-bundle/v/stable.png)](https://packagist.org/packages/mcfedr/beanstalk-queue-driver-bundle)
[![License](https://poser.pugx.org/mcfedr/beanstalk-queue-driver-bundle/license.png)](https://packagist.org/packages/mcfedr/beanstalk-queue-driver-bundle)
[![Build Status](https://travis-ci.org/mcfedr/beanstalk-queue-driver-bundle.svg?branch=master)](https://travis-ci.org/mcfedr/beanstalk-queue-driver-bundle)

## Usage

The beanstalk runner is a Symfony command. You can runner multiple instances if you need to
handle higher numbers of jobs.

```bash
./bin/console mcfedr:queue:{name}-runner
```

Where `{name}` is what you used in the config. Add `-v` or more to get detailed logs.

## Install

### Composer

    php composer.phar require mcfedr/beanstalk-queue-driver-bundle

### AppKernel

Include the bundle in your AppKernel

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Mcfedr\BeanstalkQueueDriverBundle\McfedrBeanstalkQueueDriverBundle(),

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

## Options to `QueueManager::put`

* `queue` - The name of the queue to put the job in
* `priority` - The job priority
* `ttr` - Beanstalk Time to run, the time given for a job to finish before it is repeated
* `time` - A `\DateTime` object of when to schedule this job
* `delay` - Number of seconds from now to schedule this job

