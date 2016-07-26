<?php
/**
 * Created by mcfedr on 21/03/2014 11:30
 */

namespace Mcfedr\BeanstalkQueueDriverBundle\Queue;

use Mcfedr\QueueManagerBundle\Queue\AbstractJob;

class BeanstalkJob extends AbstractJob
{
    private $id;

    public function __construct($name, array $arguments, array $options, $id)
    {
        parent::__construct($name, $arguments, $options);
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
