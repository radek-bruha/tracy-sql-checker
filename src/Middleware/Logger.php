<?php declare(strict_types=1);

namespace Bruha\Tracy\Middleware;

use Bruha\Tracy\Query;

final class Logger
{

    /**
     * @var Query[]
     */
    private array $queries = [];

    /**
     * @param mixed[] $parameters
     * @param mixed[] $backtraces
     */
    public function addQuery(string $query, float $duration, array $parameters, array $backtraces): void
    {
        $this->queries[] = new Query($query, $duration, $parameters, $backtraces);
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

}
