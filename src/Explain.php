<?php declare(strict_types=1);

namespace Bruha\Tracy;

/**
 * Class Explain
 *
 * @package Bruha\Tracy
 */
final class Explain
{

    /**
     * Explain constructor
     *
     * @param string $table
     * @param string $type
     * @param string $keys
     * @param string $key
     * @param int    $rows
     * @param int    $filtered
     * @param string $extra
     */
    public function __construct(
        private readonly string $table,
        private readonly string $type,
        private readonly string $keys,
        private readonly string $key,
        private readonly int $rows,
        private readonly int $filtered,
        private readonly string $extra
    ) {
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getKeys(): string
    {
        return $this->keys;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return int
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * @return int
     */
    public function getFiltered(): int
    {
        return $this->filtered;
    }

    /**
     * @return string
     */
    public function getExtra(): string
    {
        return $this->extra;
    }

}
