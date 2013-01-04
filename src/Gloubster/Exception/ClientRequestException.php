<?php

namespace Gloubster\Exception;

class ClientRequestException extends RuntimeException implements ExceptionInterface
{
    private $response;

    public function __construct($message, $response)
    {
        parent::__construct($message);
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
