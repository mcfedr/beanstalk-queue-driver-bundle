<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\Manager;

use Mcfedr\BeanstalkQueueDriverBundle\Queue\BeanstalkJob;
use Mcfedr\QueueManagerBundle\Exception\NoSuchJobException;
use Mcfedr\QueueManagerBundle\Exception\WrongJobException;
use Mcfedr\QueueManagerBundle\Manager\QueueManager;
use Mcfedr\QueueManagerBundle\Queue\Job;
use Pheanstalk\Exception\ServerException;
use Pheanstalk\PheanstalkInterface;

class BeanstalkQueueManager implements QueueManager
{
    use PheanstalkClientTrait;

    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    public function put($name, array $arguments = [], array $options = [])
    {
        $queue = isset($options['queue']) ? $options['queue'] : $this->defaultQueue;
        $priority = isset($options['priority']) ? $options['priority'] : PheanstalkInterface::DEFAULT_PRIORITY;
        if (isset($options['time'])) {
            $seconds = ($s = $options['time']->getTimestamp() - time()) > 0 ? $s : 0;
        } elseif (isset($options['delay'])) {
            $seconds = $options['delay'];
        } else {
            $seconds = PheanstalkInterface::DEFAULT_DELAY;
        }
        $ttr = isset($options['ttr']) ? $options['ttr'] : PheanstalkInterface::DEFAULT_TTR;

        $job = new BeanstalkJob($name, $arguments, $priority, $ttr);
        $id = $this->pheanstalk->useTube($queue)->put($job->getData(), $priority, $seconds, $ttr);
        $job->setId($id);

        return $job;
    }

    public function delete(Job $job)
    {
        if (!($job instanceof BeanstalkJob)) {
            throw new WrongJobException('Beanstalk manager can only delete beanstalk jobs');
        }

        try {
            $this->pheanstalk->delete($job->getId());
        } catch (ServerException $e) {
            throw new NoSuchJobException('Error deleting job', 0, $e);
        }
    }
}
