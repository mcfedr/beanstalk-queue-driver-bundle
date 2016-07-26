<?php
/**
 * Created by mcfedr on 04/02/2016 09:18
 */

namespace Mcfedr\ResqueQueueDriverBundle\Tests\Resque\Job;

use Mcfedr\ResqueQueueDriverBundle\Resque\Job;

class JobTest extends \PHPUnit_Framework_TestCase
{
    public function testPerform()
    {
        $job = new Job();
        $job->args = [
            'name' => 'test_worker',
            'arguments' => ['first' => 1, 'second' => 'second'],
            'kernel_options' => [
                'kernel.root_dir'=> '../../../../tests/',
                'kernel.environment' => 'test',
                'kernel.debug'=> true
            ]
        ];
        $job->perform();
    }
}
