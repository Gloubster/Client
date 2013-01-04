<?php

namespace Gloubster\Exception;

use Gloubster\Message\Acknowledgement\JobNotAcknowledgement;

class ClientNotAcknowledgedRequestException extends ClientRequestException
{
    private $nack;

    public function __construct($message, $response, JobNotAcknowledgement $nack)
    {
        parent::__construct($message, $response);
        $this->nack = $nack;
    }

    public function getAcknowledgement()
    {
        return $this->nack;
    }
}
