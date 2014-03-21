<?php
/**
 * Created by mcfedr on 21/03/2014 11:07
 */

namespace mcfedr\Queue\Driver\PheanstalkBundle\Manager;

use mcfedr\Queue\Driver\PheanstalkBundle\Queue\PheanstalkJob;
use mcfedr\Queue\QueueManagerBundle\Manager\QueueManager;
use mcfedr\Queue\QueueManagerBundle\Exception\WrongJobException;
use mcfedr\Queue\QueueManagerBundle\Queue\Job;

class PheanstalkQueueManager implements QueueManager
{
    /**
     * @var \Pheanstalk_Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var string
     */
    protected $defaultQueue;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!isset($options['port'])) {
            $options['port'] = \Pheanstalk_Pheanstalk::DEFAULT_PORT;
        }

        $this->pheanstalk = new \Pheanstalk_Pheanstalk(
            $options['host'],
            $options['post']
        );

        if (isset($options['default_queue'])) {
            $this->defaultQueue = $options['default_queue'];
        }
        else {
            $this->defaultQueue = 'default';
        }
    }

    /**
     * Put a new job on a queue
     *
     * @param string $jobData
     * @param string $queue optional queue name, otherwise the default queue will be used
     * @param null $priority
     * @param \DateTime $when
     * @return Job
     */
    public function put($jobData, $queue = null, $priority = null, $when = null)
    {
        if (!$queue) {
            $queue = $this->defaultQueue;
        }
        if (!$priority) {
            $priority = \Pheanstalk_Pheanstalk::DEFAULT_PRIORITY;
        }
        if ($when) {
            $seconds = $when->getTimestamp() - time();
        }
        else {
            $seconds = 0;
        }
        $this->pheanstalk->useTube($queue)->put($jobData, $priority, $seconds);
    }

    /**
     * Get the next job from the queue
     *
     * @param string $queue optional queue name, otherwise the default queue will be used
     * @return Job
     */
    public function get($queue = null)
    {
        if (!$queue) {
            $queue = $this->defaultQueue;
        }
        return new PheanstalkJob($this->pheanstalk->watch($queue)->reserve());
    }

    /**
     * Remove a job, you should call this when you have finished processing a job
     *
     * @param \mcfedr\Queue\QueueManagerBundle\Queue\Job $job
     * @throws \mcfedr\Queue\QueueManagerBundle\Exception\WrongJobException
     */
    public function delete(Job $job)
    {
        if (!($job instanceof PheanstalkJob)) {
            throw new WrongJobException();
        }
        $this->pheanstalk->delete($job->getJob());
    }
}