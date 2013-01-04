# Gloubster PHP Client

[![Build Status](https://travis-ci.org/Gloubster/Client.png?branch=master)](https://travis-ci.org/Gloubster/Client)

A set of synchronous / asynchronous clients to use to query a Gloubster Server.

## Use in your project

Use [composer](http://getcomposer.org/) to add it in your project :

```json
{
    "require": {
        "gloubster/client": "0.1.*"
    }
}
```

## Synchronous Clients

Synchronous clients are commonly used like in the following example,
specific clients use are described below.

```php
use Gloubster\Exception\ClientRequestException;
use Gloubster\Exception\ClientNotAcknowledgedRequestException;

try {
    $acknowledgement = $client->send($job);
} catch (ClientRequestException $e) {
    echo "An error occured while querying Gloubster Server, the response was not understood\n";
    echo sprintf("Server answered : %s\n", $e->getResponse());
} catch (ClientNotAcknowledgedRequestException $e) {
    echo sprintf("Gloubster Server did not acknowledge the query for "
        . "the following reason : %s \n", $e->getAcknowledgement()->getReason());
}
```

### ZeroMQ Client

ZeroMQ client is very easy to use ; you just to have to provide `$transport`,
`$host` and `$port`.

```php
use Gloubster\Client\Sync\ZMQClient;

// Will connect to ZMQ socket tcp://localhost:12300
$client = ZMQClient::create('tcp', 'localhost', '12300');
```

## Asynchronous Clients

// todo

## License

Gloubster PHP Client is released under the MIT license.
