<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Search\Model;

/**
 * Something a store search finds besides products (categories, pages,
 * brands, posts): a heading and the matches, best first; a match's note
 * tells it apart (where a category is). A match may carry the cache tags of
 * what it links (cat_c_12, cms_p_3): the full page cached suggestions are
 * then cleaned when that category or page changes.
 */
interface SourceInterface
{
    public function title(): string;

    /**
     * @return array<int, array{name: string, url: string, note?: string, tags?: string[]}>
     */
    public function find(string $query, int $storeId, int $limit): array;
}
