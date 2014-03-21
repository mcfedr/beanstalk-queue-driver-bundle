<?php
/**
 * Created by mcfedr on 21/03/2014 11:30
 */

namespace mcfedr\Queue\Driver\PheanstalkBundle\Queue;

use mcfedr\Queue\QueueManagerBundle\Queue\Job;

class PheanstalkJob implements Job
{
    /**
     * @var \Pheanstalk_Job
     */
    protected $job;

    public function __construct(\Pheanstalk_Job $job)
    {
        $this->job = $job;
    }


    /**
     * Get the data for this job
     *
     * @return string
     */
    public function getData()
    {
        return $this->job->getData();
    }

    /**
     * @return \Pheanstalk_Job
     */
    public function getJob()
    {
        return $this->job;
    }
}