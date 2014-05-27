<?php
/**
 * Created by mcfedr on 21/03/2014 11:07
 */

namespace mcfedr\Queue\Driver\PheanstalkBundle\Manager;

use mcfedr\Queue\Driver\PheanstalkBundle\Queue\PheanstalkJob;
use mcfedr\Queue\QueueManagerBundle\Exception\NoSuchJobException;
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
    public function __construct(array $options = [])
    {
        if (!isset($options['port'])) {
            $options['port'] = \Pheanstalk_Pheanstalk::DEFAULT_PORT;
        }

        if (!isset($options['host'])) {
            $options['host'] = '127.0.0.1';
        }

        $this->pheanstalk = new \Pheanstalk_Pheanstalk(
            $options['host'],
            $options['port']
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
            $seconds = ($s = $when->getTimestamp() - time()) > 0 ? $s : 0;
        }
        else {
            $seconds = 0;
        }
        $id = $this->pheanstalk->useTube($queue)->put($jobData, $priority, $seconds);
        return new PheanstalkJob(new \Pheanstalk_Job($id, $jobData));
    }

    /**
     * Get the next job from the queue
     *
     * @param string $queue optional queue name, otherwise the default queue will be used
     * @param int $timeout
     * @return Job
     */
    public function get($queue = null, $timeout = null)
    {
        if (!$queue) {
            $queue = $this->defaultQueue;
        }
        $job = $this->pheanstalk->watch($queue)->reserve($timeout);
        if ($job) {
            return new PheanstalkJob($job);
        }
        return false;
    }

    /**
     * Remove a job, you should call this when you have finished processing a job
     *
     * @param \mcfedr\Queue\QueueManagerBundle\Queue\Job $job
     * @throws \mcfedr\Queue\QueueManagerBundle\Exception\NoSuchJobException
     * @throws \mcfedr\Queue\QueueManagerBundle\Exception\WrongJobException
     */
    public function delete(Job $job)
    {
        if (!($job instanceof PheanstalkJob)) {
            throw new WrongJobException();
        }
        try {
            $this->pheanstalk->delete($job->getJob());
        } catch (Pheanstalk_Exception_ServerException $e) {
            throw new NoSuchJobException("Error deleting job", 0, $e);
        }
    }
}