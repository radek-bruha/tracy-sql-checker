<?php declare(strict_types=1);

namespace Bruha\Tracy\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;

final readonly class LoggingMiddleware implements Middleware
{

    public function __construct(private Logger $logger)
    {
    }

    public function wrap(Driver $driver): Driver
    {
        return new LoggingDriver($driver, $this->logger);
    }

}
