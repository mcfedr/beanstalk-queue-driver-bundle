<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\Manager;

use Pheanstalk\Pheanstalk;

trait PheanstalkClientTrait
{
    /**
     * @var Pheanstalk
     */
    private $pheanstalk;

    /**
     * @var string
     */
    private $defaultQueue;

    private function setOptions(array $options)
    {
        $this->pheanstalk = new Pheanstalk(
            $options['host'],
            $options['port'],
            $options['connection']['timeout'],
            $options['connection']['persistent']
        );

        $this->defaultQueue = $options['default_queue'];
    }
}
