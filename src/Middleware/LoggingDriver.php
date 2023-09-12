<?php declare(strict_types=1);

namespace Bruha\Tracy\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

final class LoggingDriver extends AbstractDriverMiddleware
{

    public function __construct(Driver $wrappedDriver, private readonly Logger $logger)
    {
        parent::__construct($wrappedDriver);
    }

    /**
     * @param mixed[] $params
     */
    public function connect(array $params): LoggingConnection
    {
        return new LoggingConnection(parent::connect($params), $this->logger);
    }

}
