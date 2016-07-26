<?php
/**
 * Created by mcfedr on 05/02/2016 21:26
 */

namespace Mcfedr\BeanstalkQueueDriverBundle\Command;

use Mcfedr\QueueManagerBundle\Queue\Worker;
use Pheanstalk\Pheanstalk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BeanstalkCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var Pheanstalk
     */
    private $pheanstalk;

    /**
     * @var string
     */
    private $defaultQueue;

    public function __construct(array $options)
    {
        parent::__construct();
        $this->pheanstalk = new Pheanstalk(
            $options['host'],
            $options['port'],
            $options['connection']['timeout'],
            $options['connection']['persistent']
        );

        $this->defaultQueue = $options['default_queue'];
    }

    protected function configure()
    {
        $this
            ->setName('mcfedr:beanstalk:worker')
            ->setDescription('Run a beanstalk worker')
            ->addArgument('queue', InputArgument::OPTIONAL, 'The queue to watch', $this->defaultQueue);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $job = $this->pheanstalk->watch($input->getArgument('queue'))->reserve();

        $data = $job->getData();
        if (!$this->container->has($data['name'])) {

        }

        /** @var Worker $worker */
        $worker = $this->container->get($data['name']);
        $worker->execute($data['arguments']);

        $this->pheanstalk->delete($job);
    }
}
