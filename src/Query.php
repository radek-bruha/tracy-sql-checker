<?php declare(strict_types=1);

namespace Bruha\Tracy;

use DateTimeImmutable;

/**
 * Class Query
 *
 * @package Bruha\Tracy
 */
final class Query
{

    /**
     * Query constructor
     */
    public function __construct()
    {
        $this->timestamp = new DateTimeImmutable();
    }

    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $timestamp;

    /**
     * @var string
     */
    private string $query;

    /**
     * @var float
     */
    private float $duration;

    /**
     * @var Explain[]
     */
    private array $explain = [];

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @param DateTimeImmutable $timestamp
     *
     * @return Query
     */
    public function setTimestamp(DateTimeImmutable $timestamp): Query
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     *
     * @return Query
     */
    public function setQuery(string $query): Query
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     *
     * @return Query
     */
    public function setDuration(float $duration): Query
    {
        $this->duration = $duration;

        return $this;
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
     *
     * @return Query
     */
    public function setExplain(array $explain): Query
    {
        $this->explain = $explain;

        return $this;
    }

}
