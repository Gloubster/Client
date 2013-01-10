<?php

/*
 * This file is part of Gloubster.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gloubster\Client\Sync;

use Gloubster\Exception\ClientRequestException;
use Gloubster\Exception\ClientNotAcknowledgedRequestException;
use Gloubster\Exception\RuntimeException;
use Gloubster\Message\Job\JobInterface;
use Gloubster\Message\Acknowledgement\Factory as AcknowledgementFactory;
use Gloubster\Message\Acknowledgement\JobAcknowledgement;

class ZMQClient implements ClientInterface
{
    private $transport;
    private $host;
    private $port;

    private $context;
    private $socket;

    public function __construct(\ZMQContext $context, $transport, $host, $port)
    {
        $this->transport = $transport;
        $this->host = $host;
        $this->port = $port;

        $this->context = $context;

        $this->socket = $this->context->getSocket(\ZMQ::SOCKET_REQ, 'gloubster-client');
        $this->socket->connect(sprintf("%s://%s:%s", $this->transport, $this->host, $this->port));
        $this->socket->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, 1000);
        $this->socket->setSockOpt(\ZMQ::SOCKOPT_RCVTIMEO, 1000);
        $this->socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 1000);
    }

    public function __destruct()
    {
        $this->socket->disconnect(sprintf("%s://%s:%s", $this->transport, $this->host, $this->port));
    }

    /**
     * {@inheritdoc}
     */
    public function send(JobInterface $job)
    {
        $this->socket->send($job->toJson());
        $response = $this->socket->recv();

        try {
            $acknowledgement = AcknowledgementFactory::fromJson($response);
        } catch (RuntimeException $e) {
            throw new ClientRequestException(
                'Message was not understood by gloubster server', $response
            );
        }

        if (!$acknowledgement instanceof JobAcknowledgement) {
            throw new ClientNotAcknowledgedRequestException(
                'Message was not acknowledge by gloubster server'
                , $response, $acknowledgement
            );
        }

        return $acknowledgement;
    }

    public static function create($transport, $host, $port)
    {
        if (!extension_loaded('ZMQ')) {
            throw new RuntimeException('ZMQ extension is required to use Gloubster ZMQ client');
        }

        return new static(new \ZMQContext(), $transport, $host, $port);
    }
}
