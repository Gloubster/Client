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

use Silex\Application;
use Silex\ServiceProviderInterface;

class GloubsterClientServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['gloubster.client.transport'] = 'tcp';
        $app['gloubster.client.host'] = '127.0.0.1';
        $app['gloubster.client.port'] = '22345';

        $app['gloubster.client.context'] = $app->share(function (Application $app) {
            return new \ZMQContext();
        });

        $app['gloubster.client'] = $app->share(function (Application $app) {
            return new ZMQClient(
                $app['gloubster.client.context'],
                $app['gloubster.client.transport'],
                $app['gloubster.client.host'],
                $app['gloubster.client.port']
            );
        });
    }

    public function boot(Application $app)
    {
    }
}
