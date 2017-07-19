<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\Queue;

use Mcfedr\QueueManagerBundle\Queue\AbstractRetryableJob;
use Pheanstalk\Job;

class BeanstalkJob extends AbstractRetryableJob
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Job
     */
    private $job;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var int
     */
    private $ttr;

    public function __construct($name, array $arguments, $priority, $ttr, $id = null, $retryCount = 0, Job $job = null)
    {
        parent::__construct($name, $arguments, $retryCount);
        $this->id = $id;
        $this->job = $job;
        $this->priority = $priority;
        $this->ttr = $ttr;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return BeanstalkJob
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return int
     */
    public function getTtr()
    {
        return $this->ttr;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    public function getData()
    {
        return json_encode([
            'name' => $this->getName(),
            'arguments' => $this->getArguments(),
            'retryCount' => $this->getRetryCount(),
            'priority' => $this->getPriority(),
            'ttr' => $this->getTtr(),
        ]);
    }
}
