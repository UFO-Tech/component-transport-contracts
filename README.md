# UFO Transport Contracts

Shared asynchronous transport contracts and resolver factory for Symfony Messenger.
Requires PHP 8.3+ and Symfony Messenger ^7.2.

```sh
composer require ufo-tech/component-transport-contracts
```

## Usage

Implement `Ufo\Component\TransportContracts\AsyncTransportResolverInterface` or
extend `AbstractAsyncTransportResolver`, then pass your resolvers to the factory:

```php
use Ufo\Component\TransportContracts\AsyncStampDTO;
use Ufo\Component\TransportContracts\RPCAsyncTransportFactory;

$factory = new RPCAsyncTransportFactory($resolvers);
$dsn = 'amqp://localhost/%2f/messages';
$resolver = $factory->getTransportResolver($dsn);
$transport = $resolver->getTransport($dsn);
$stamp = $resolver->createAsyncStamp(new AsyncStampDTO($dsn));
```

Install the Messenger bridge for your transport separately (e.g. `symfony/amqp-messenger`).

## Tests

```sh
composer install
composer test
```

MIT license. See [LICENSE](LICENSE).
