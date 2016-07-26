<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\Tests\DependencyInjection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Created by mcfedr on 04/02/2016 09:48
 */
class McfedrBeanstalkQueueDriverExtensionTest extends WebTestCase
{
    public function testConfiguration()
    {
        $client = static::createClient();
        $this->assertTrue($client->getContainer()->has('mcfedr_queue_manager.default'));
    }
}
