<?php

namespace Mcfedr\BeanstalkQueueDriverBundle\Command;

use Mcfedr\BeanstalkQueueDriverBundle\Manager\PheanstalkClientTrait;
use Mcfedr\BeanstalkQueueDriverBundle\Queue\BeanstalkJob;
use Mcfedr\QueueManagerBundle\Command\RunnerCommand;
use Mcfedr\QueueManagerBundle\Exception\UnexpectedJobDataException;
use Mcfedr\QueueManagerBundle\Manager\QueueManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BeanstalkCommand extends RunnerCommand
{
    use ContainerAwareTrait;
    use PheanstalkClientTrait;

    public function __construct($name, array $options, QueueManager $queueManager)
    {
        parent::__construct($name, $options, $queueManager);
        $this->setOptions($options);
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'The queue to watch, can be a comma separated list');
    }

    protected function handleInput(InputInterface $input)
    {
        if (($queues = $input->getOption('queue'))) {
            foreach (explode(',', $queues) as $queue) {
                $this->pheanstalk->watch($queue);
            }
        } else {
            $this->pheanstalk->watch($this->defaultQueue);
        }
    }

    protected function getJobs()
    {
        $job = $this->pheanstalk->reserve();
        $data = json_decode($job->getData(), true);
        if (!is_array($data) || !isset($data['name']) || !isset($data['arguments']) || !isset($data['retryCount']) || !isset($data['priority']) || !isset($data['ttr'])) {
            $this->pheanstalk->delete($job);

            throw new UnexpectedJobDataException('Beanstalkd message missing data fields name, arguments, retryCount, priority and ttr');
        }

        return [new BeanstalkJob($data['name'], $data['arguments'], $data['priority'], $data['ttr'], $job->getId(), $data['retryCount'], $job)];
    }

    protected function finishJobs(array $okJobs, array $retryJobs, array $failedJobs)
    {
        /** @var BeanstalkJob $job */
        foreach ($okJobs as $job) {
            $this->pheanstalk->delete($job->getJob());
        }

        /** @var BeanstalkJob $job */
        foreach ($retryJobs as $job) {
            $this->pheanstalk->delete($job->getJob());
            $job->incrementRetryCount();
            $this->pheanstalk->put($job->getData(), $job->getPriority(), $job->getRetryCount() * $job->getRetryCount() * 30, $job->getTtr());
        }

        /** @var BeanstalkJob $job */
        foreach ($failedJobs as $job) {
            $this->pheanstalk->delete($job->getJob());
        }
    }
}
