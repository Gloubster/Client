<?php

/*
 * This file is part of Gloubster.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
