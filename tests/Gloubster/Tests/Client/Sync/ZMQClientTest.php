<?php

namespace Gloubster\Tests\Sync;

use Gloubster\Client\Sync\ZMQClient;
use Gloubster\Exception\ClientRequestException;
use Gloubster\Exception\ClientNotAcknowledgedRequestException;
use Gloubster\Message\Acknowledgement\JobAcknowledgement;
use Gloubster\Message\Acknowledgement\JobNotAcknowledgement;

class ZMQClientTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldSendMessageAndReceiveAcknowledgement()
    {
        $transport = 'ipc';
        $host = 'local.gloubster';
        $port = '1234';

        $context = $this->getContext();
        $socket = $this->getSocket();

        $context->expects($this->once())
            ->method('getSocket')
            ->with($this->equalTo(\ZMQ::SOCKET_REQ), $this->anything())
            ->will($this->returnValue($socket));

        $job = $this->getMockBuilder('Gloubster\\Message\\Job\\ImageJob')
            ->disableOriginalConstructor()
            ->getMock();

        $ack = new JobAcknowledgement();

        $json = '{"hello": "world !"}';

        $socket->expects($this->once())
            ->method('send')
            ->with($json);

        $socket->expects($this->once())
            ->method('recv')
            ->will($this->returnValue($ack->toJson()));

        $job->expects($this->any())
            ->method('toJson')
            ->will($this->returnValue($json));

        $client = new ZMQClient($context, $transport, $host, $port);
        $this->assertEquals($ack, $client->send($job));
    }

    /** @test */
    public function itShouldThrowANotAcknowledgeExceptionifNonAcknowledge()
    {
        $transport = 'ipc';
        $host = 'local.gloubster';
        $port = '1234';

        $context = $this->getContext();
        $socket = $this->getSocket();

        $context->expects($this->once())
            ->method('getSocket')
            ->with($this->equalTo(\ZMQ::SOCKET_REQ), $this->anything())
            ->will($this->returnValue($socket));

        $job = $this->getMockBuilder('Gloubster\\Message\\Job\\ImageJob')
            ->disableOriginalConstructor()
            ->getMock();

        $nack = new JobNotAcknowledgement();

        $json = '{"hello": "world !"}';

        $socket->expects($this->once())
            ->method('send')
            ->with($json);

        $socket->expects($this->once())
            ->method('recv')
            ->will($this->returnValue($nack->toJson()));

        $job->expects($this->any())
            ->method('toJson')
            ->will($this->returnValue($json));

        $client = new ZMQClient($context, $transport, $host, $port);

        try {
            $client->send($job);
        } catch (ClientNotAcknowledgedRequestException $e) {
            $this->assertInstanceOf('Gloubster\\Exception\\ClientNotAcknowledgedRequestException', $e);
            $this->assertEquals($nack->toJson(), $e->getResponse());
            $this->assertEquals($nack, $e->getAcknowledgement());
        }
    }

    /** @test */
    public function itShouldThrowANotAcknowledgeExceptionOnFailure()
    {
        $transport = 'ipc';
        $host = 'local.gloubster';
        $port = '1234';

        $context = $this->getContext();
        $socket = $this->getSocket();

        $context->expects($this->once())
            ->method('getSocket')
            ->with($this->equalTo(\ZMQ::SOCKET_REQ), $this->anything())
            ->will($this->returnValue($socket));

        $job = $this->getMockBuilder('Gloubster\\Message\\Job\\ImageJob')
            ->disableOriginalConstructor()
            ->getMock();

        $response = 'dudi du di';

        $json = '{"hello": "world !"}';

        $socket->expects($this->once())
            ->method('send')
            ->with($json);

        $socket->expects($this->once())
            ->method('recv')
            ->will($this->returnValue($response));

        $job->expects($this->any())
            ->method('toJson')
            ->will($this->returnValue($json));

        $client = new ZMQClient($context, $transport, $host, $port);

        try {
            $client->send($job);
        } catch (ClientRequestException $e) {
            $this->assertInstanceOf('Gloubster\\Exception\\ClientRequestException', $e);
            $this->assertEquals($response, $e->getResponse());
        }
    }

    /** @test */
    public function constructMustConstruct()
    {
        $client = ZMQClient::create('ipc', 'localhost', 14233);
        $this->assertInstanceOf('Gloubster\\Client\\Sync\\ZMQClient', $client);
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
