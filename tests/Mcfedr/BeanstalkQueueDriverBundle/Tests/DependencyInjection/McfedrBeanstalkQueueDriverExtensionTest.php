<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\Tests\DependencyInjection;

use Mcfedr\BeanstalkQueueDriverBundle\Manager\BeanstalkQueueManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class McfedrBeanstalkQueueDriverExtensionTest extends WebTestCase
{
    public function testConfiguration()
    {
        $client = static::createClient();
        $this->assertTrue($client->getContainer()->has(BeanstalkQueueManager::class));
        $this->assertTrue($client->getContainer()->has('mcfedr_queue_manager.default'));
    }
}
