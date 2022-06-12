<?php declare(strict_types=1);

namespace Bruha\Tracy;

use Doctrine\DBAL\Logging\SQLLogger as BaseSqlLogger;

/**
 * Class SqlLogger
 *
 * @package Bruha\Tracy
 */
final class SqlLogger implements BaseSqlLogger
{

    /**
     * @var Query[]
     */
    private array $queries = [];

    /**
     * @var int
     */
    private int $counter = 0;

    /**
     * @param mixed        $query
     * @param mixed[]|null $parameters
     * @param mixed[]|null $types
     */
    public function startQuery($query, ?array $parameters = NULL, ?array $types = NULL): void
    {
        if (str_contains($query, 'SELECT')) {
            if ($parameters) {
                $query = sprintf('%s;', sprintf(str_replace('?', "'%s'", $query), ...$parameters));
            } else {
                $query = sprintf('%s;', str_replace('?', "'%s'", $query));
            }

            $this->queries[++$this->counter] = (new Query())->setQuery($query)->setDuration(microtime(TRUE));
        }
    }

    /**
     *
     */
    public function stopQuery(): void
    {
        $this->queries[$this->counter]->setDuration(microtime(TRUE) - $this->queries[$this->counter]->getDuration());
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

}
