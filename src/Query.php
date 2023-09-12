<?php declare(strict_types=1);

namespace Bruha\Tracy;

use DateTimeImmutable;
use DateTimeInterface;

final class Query
{

    private DateTimeImmutable $timestamp;

    /**
     * @var Explain[]
     */
    private array $explain = [];

    /**
     * @param mixed[]   $parameters
     * @param mixed[][] $backtraces
     */
    public function __construct(
        private readonly string $query,
        private readonly float $duration,
        private readonly array $parameters,
        private readonly array $backtraces,
    ) {
        $this->timestamp = new DateTimeImmutable();
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return mixed[]
     */
    public function getBacktraces(): array
    {
        return $this->backtraces;
    }

    /**
     * @return mixed[][]
     */
    public function getFormattedBacktraces(): array
    {
        $directory = sprintf('%s/', getcwd());

        return array_map(
            static fn(array $backtrace): array => [
                sprintf('%s::%s', $backtrace['class'], $backtrace['function']),
                isset($backtrace['file']) && isset($backtrace['line']) ? sprintf('%s:%s', str_replace($directory, '', $backtrace['file']), $backtrace['line']) : '',
            ],
            $this->backtraces,
        );
    }

    /**
     * @return Explain[]
     */
    public function getExplain(): array
    {
        return $this->explain;
    }

    /**
     * @param Explain[] $explain
     */
    public function setExplain(array $explain): Query
    {
        $this->explain = $explain;

        return $this;
    }

    public function addExplain(Explain $explain): Query
    {
        $this->explain[] = $explain;

        return $this;
    }

    public function getExplainQuery(): string
    {
        return sprintf('EXPLAIN EXTENDED %s', $this->query);
    }

    public function getParameterizedQuery(): string
    {
        $parameters = $this->parameters;

        foreach ($parameters as $key => $parameter) {
            if ($parameter instanceof DateTimeInterface) {
                $parameters[$key] = $parameter->format('Y-m-d H:i:s');
            }
        }

        return sprintf('%s;', sprintf(str_replace('?', "'%s'", $this->query), ...$parameters));
    }

}
