<?php

namespace App\Services\Meta\Data;

class PaginatedResult
{
    public function __construct(
        public array $items,
        public ?string $nextCursor,
        public ?string $previousCursor,
        public int $count,
        public ?string $next = null,
        public ?string $previous = null
    ) {}

    public static function fromMetaResponse(array $response): self
    {
        $paging = $response['paging'] ?? [];
        $cursors = $paging['cursors'] ?? [];

        return new self(
            items: $response['data'] ?? [],
            nextCursor: $cursors['after'] ?? null,
            previousCursor: $cursors['before'] ?? null,
            count: count($response['data'] ?? []),
            next: $paging['next'] ?? null,
            previous: $paging['previous'] ?? null
        );
    }

    public function hasMore(): bool
    {
        return $this->next !== null;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function hasNextPage(): bool
    {
        return $this->nextCursor !== null;
    }

    public function hasPreviousPage(): bool
    {
        return $this->previousCursor !== null;
    }
} 