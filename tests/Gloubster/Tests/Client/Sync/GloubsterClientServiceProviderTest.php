<?php

namespace Gloubster\Tests\Client\Sync;

use Silex\Application;
use Gloubster\Client\Sync\GloubsterClientServiceProvider;

class GloubsterClientServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldProvideGloubsterClientAfterRegistering()
    {
        $app = new Application();
        $app->register(new GloubsterClientServiceProvider());

        $context = $this->getContext();
        $socket = $this->getSocket(
            $app['gloubster.client.transport'],
            $app['gloubster.client.host'],
            $app['gloubster.client.port']
        );

        $context->expects($this->once())
            ->method('getSocket')
            ->with($this->equalTo(\ZMQ::SOCKET_REQ), $this->anything())
            ->will($this->returnValue($socket));

        $app['gloubster.client.context'] = $context;

        $this->assertInstanceOf('Gloubster\Client\Sync\ZMQClient', $app['gloubster.client']);
    }

    private function getSocket($transport = 'ipc', $host = 'local.gloubster', $port = '1234')
    {
        $socket = $this->getMockBuilder('ZMQSocket')
            ->disableOriginalConstructor()
            ->getMock();

        $socket->expects($this->once())
            ->method('connect')
            ->with(sprintf("%s://%s:%s", $transport, $host, $port));

        return $socket;
    }

    private function getContext()
    {
        return $this->getMockBuilder('ZMQContext')
                ->disableOriginalConstructor()
                ->getMock();
    }
}
