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

use Gloubster\Message\Job\JobInterface;
use Gloubster\Message\Acknowledgement\JobAcknowledgement;
use Gloubster\Exception\ClientRequestException;
use Gloubster\Exception\ClientNotAcknowledgedRequestException;

interface ClientInterface
{
    /**
     *
     * @param JobInterface $job
     *
     * @return JobAcknowledgement
     *
     * @throws ClientNotAcknowledgedRequestException
     * @throws ClientRequestException
     */
    public function send(JobInterface $job);
}