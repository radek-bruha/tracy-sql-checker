<?php declare(strict_types=1);

namespace Bruha\Tracy;

final class Explain
{

    /**
     * @param string[] $keys
     */
    public function __construct(
        private readonly string $table,
        private readonly string $type,
        private readonly array $keys,
        private readonly string $key,
        private readonly int $rows,
        private readonly int $filtered,
        private readonly string $extra,
    ) {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getFiltered(): int
    {
        return $this->filtered;
    }

    public function getExtra(): string
    {
        return $this->extra;
    }

}
