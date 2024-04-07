<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DTO\FormData;

class LibraryFormDto
{
    private ?string $query = null;
    private ?string $page = null;
    private ?string $size = null;

    /**
     * @var array<string|int, mixed>
     */
    private array $filters = [];
    private ?string $sort_by = null;
    private ?string $sort_direction = null;

    /**
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * @param string|null $query
     */
    public function setQuery(?string $query): void
    {
        $this->query = $query;
    }

    /**
     * @return string|null
     */
    public function getPage(): ?string
    {
        return $this->page;
    }

    /**
     * @param string|null $page
     */
    public function setPage(?string $page): void
    {
        $this->page = $page;
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @param string|null $size
     */
    public function setSize(?string $size): void
    {
        $this->size = $size;
    }

    /**
     * @return array<string|int|null, mixed>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param mixed $filter
     *
     * @return array<string|int|null, mixed>
     */
    public function addFilter(mixed $filter): array
    {
        $this->filters[] = $filter;

        return $this->filters;
    }

    /**
     * @param array<string|int|null, mixed> $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return string|null
     */
    public function getSortBy(): ?string
    {
        return $this->sort_by;
    }

    /**
     * @param string|null $sort_by
     */
    public function setSortBy(?string $sort_by): void
    {
        $this->sort_by = $sort_by;
    }

    /**
     * @return string|null
     */
    public function getSortDirection(): ?string
    {
        return $this->sort_direction;
    }

    /**
     * @param string|null $sort_direction
     */
    public function setSortDirection(?string $sort_direction): void
    {
        $this->sort_direction = $sort_direction;
    }
}
