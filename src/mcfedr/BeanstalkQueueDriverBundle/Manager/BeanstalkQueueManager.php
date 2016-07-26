<?php
/**
 * Created by mcfedr on 21/03/2014 11:07
 */

namespace Mcfedr\BeanstalkQueueDriverBundle\Manager;

use Mcfedr\BeanstalkQueueDriverBundle\Queue\BeanstalkJob;
use Mcfedr\QueueManagerBundle\Exception\NoSuchJobException;
use Mcfedr\QueueManagerBundle\Manager\QueueManager;
use Mcfedr\QueueManagerBundle\Exception\WrongJobException;
use Mcfedr\QueueManagerBundle\Queue\Job;
use Pheanstalk\Exception\ServerException;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

class BeanstalkQueueManager implements QueueManager
{
    /**
     * @var Pheanstalk
     */
    private $pheanstalk;

    /**
     * @var string
     */
    private $defaultQueue;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        $this->pheanstalk = new Pheanstalk(
            $options['host'],
            $options['port'],
            $options['connection']['timeout'],
            $options['connection']['persistent']
        );

        $this->defaultQueue = $options['default_queue'];
    }

    /**
     * {@inheritdoc}
     */
    public function put($name, array $arguments = [], array $options = [])
    {
        $queue = isset($options['queue']) ? $options['queue'] : $this->defaultQueue;
        $priority = isset($options['priority']) ? $options['priority'] : PheanstalkInterface::DEFAULT_PRIORITY;
        $seconds = isset($options['when']) ? (($s = $options['when']->getTimestamp() - time()) > 0 ? $s : 0) : PheanstalkInterface::DEFAULT_DELAY;
        $ttr = isset($options['ttr']) ? $options['ttr'] : PheanstalkInterface::DEFAULT_TTR;

        $data = [
            'name' => $name,
            'arguments' => $arguments
        ];

        $id = $this->pheanstalk->useTube($queue)->put($data, $priority, $seconds, $ttr);
        return new BeanstalkJob($name, $arguments, $options, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Job $job)
    {
        if (!($job instanceof BeanstalkJob)) {
            throw new WrongJobException();
        }

        try {
            $this->pheanstalk->delete($job->getId());
        } catch (ServerException $e) {
            throw new NoSuchJobException("Error deleting job", 0, $e);
        }
    }
}
