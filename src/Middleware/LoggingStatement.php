<?php declare(strict_types=1);

namespace Bruha\Tracy\Middleware;

use Closure;
use Doctrine\DBAL\Driver\Middleware\AbstractStatementMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;

final class LoggingStatement extends AbstractStatementMiddleware
{

    /**
     * @var mixed[]
     */
    private array $parameters = [];

    public function __construct(
        Statement $wrappedStatement,
        private readonly Logger $logger,
        private readonly string $query,
    ) {
        parent::__construct($wrappedStatement);
    }

    public function bindValue(mixed $param, mixed $value, mixed $type = ParameterType::STRING): bool
    {
        $this->parameters[$param] = $value;

        return parent::bindValue($param, $value, $type);
    }

    public function execute(mixed $params = NULL): Result
    {
        return $this->logQuery(fn(): Result => parent::execute($params), $this->query, $params);
    }

    /**
     * @param mixed[]|null $parameters
     */
    private function logQuery(Closure $closure, string $query, ?array $parameters): mixed
    {
        $time     = hrtime(TRUE);
        $result   = $closure();
        $duration = (hrtime(TRUE) - $time) / 1_000 / 1_000;

        $this->logger->addQuery(
            $query,
            $duration,
            $parameters ?? $this->parameters,
            array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2),
        );

        return $result;
    }

}
