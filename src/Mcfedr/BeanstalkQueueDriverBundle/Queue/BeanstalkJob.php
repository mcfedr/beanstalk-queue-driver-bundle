<?php
/**
 * Created by mcfedr on 21/03/2014 11:30
 */

namespace Mcfedr\BeanstalkQueueDriverBundle\Queue;

use Mcfedr\QueueManagerBundle\Queue\AbstractJob;
use Pheanstalk\Job;

class BeanstalkJob extends AbstractJob
{
    private $id;

    /**
     * @var Job
     */
    private $job;

    public function __construct($name, array $arguments, array $options, $id, Job $job = null)
    {
        parent::__construct($name, $arguments, $options);
        $this->id = $id;
        $this->job = $job;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }
}
