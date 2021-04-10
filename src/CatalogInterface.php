<?php

namespace Szabacsik\Catalog;

interface CatalogInterface
{
    /**
     * @param string $item
     * @return bool
     */
    public function add(string $item): bool;

    /**
     * @param string $regularExpressionPattern
     * @return string
     */
    public function findFirst(string $regularExpressionPattern): string;

    /**
     * @param string $regularExpressionPattern
     * @return array<int, string>
     */
    public function findAll(string $regularExpressionPattern): iterable;

    /**
     * @param string $item
     * @return int
     */
    public function remove(string $item): int;

    public function truncate(): bool;
}
