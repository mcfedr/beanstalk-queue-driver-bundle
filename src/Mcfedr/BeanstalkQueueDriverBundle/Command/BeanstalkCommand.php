<?php
/**
 * Created by mcfedr on 05/02/2016 21:26
 */

namespace Mcfedr\BeanstalkQueueDriverBundle\Command;

use Mcfedr\BeanstalkQueueDriverBundle\Manager\PheanstalkClientTrait;
use Mcfedr\BeanstalkQueueDriverBundle\Queue\BeanstalkJob;
use Mcfedr\QueueManagerBundle\Command\RunnerCommand;
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

        return [new BeanstalkJob($data['name'], $data['arguments'], [], $job->getId(), $job)];
    }

    protected function finishJobs(array $okJobs, array $retryJobs, array $failedJobs)
    {
        /** @var BeanstalkJob $job */
        foreach ($okJobs as $job)
        {
            $this->pheanstalk->delete($job->getJob());
        }

        /** @var BeanstalkJob $job */
        foreach ($retryJobs as $job)
        {
            $this->pheanstalk->release($job->getJob());
        }

        /** @var BeanstalkJob $job */
        foreach ($failedJobs as $job)
        {
            $this->pheanstalk->delete($job->getJob());
        }
    }
}
