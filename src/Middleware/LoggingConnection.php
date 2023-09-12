<?php declare(strict_types=1);

namespace Bruha\Tracy\Middleware;

use Closure;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;

final class LoggingConnection extends AbstractConnectionMiddleware
{

    public function __construct(Connection $wrappedConnection, private readonly Logger $logger)
    {
        parent::__construct($wrappedConnection);
    }

    public function prepare(string $sql): Statement
    {
        return new LoggingStatement(parent::prepare($sql), $this->logger, $sql);
    }

    public function query(string $sql): Result
    {
        return $this->logQuery(fn(): Result => parent::query($sql), $sql);
    }

    public function exec(string $sql): int
    {
        return $this->logQuery(fn(): int => parent::exec($sql), $sql);
    }

    public function beginTransaction(): bool
    {
        return $this->logQuery(fn(): bool => parent::beginTransaction(), 'BEGIN');
    }

    public function commit(): bool
    {
        return $this->logQuery(fn(): bool => parent::commit(), 'COMMIT');
    }

    public function rollBack(): bool
    {
        return $this->logQuery(fn(): bool => parent::rollBack(), 'ROLLBACK');
    }

    private function logQuery(Closure $closure, string $query): mixed
    {
        $time     = hrtime(TRUE);
        $result   = $closure();
        $duration = (hrtime(TRUE) - $time) / 1_000 / 1_000;

        $this->logger->addQuery($query, $duration, [], array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2));

        return $result;
    }

}
