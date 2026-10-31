<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Search\Test\Unit\Block;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use PHPUnit\Framework\TestCase;
use Zero\Search\Block\Results;
use Zero\Search\ViewModel\Search;

/**
 * The results page's block above the list tags the page with what it listed.
 */
class ResultsTest extends TestCase
{
    public function testIdentitiesAreTheSearchViewModels(): void
    {
        $search = $this->createStub(Search::class);
        $search->method('getIdentities')->willReturn(['cat_c_5', 'cms_p_3']);

        $this->assertSame(['cat_c_5', 'cms_p_3'], $this->block(['search' => $search])->getIdentities());
    }

    public function testNoTagsWithoutTheViewModel(): void
    {
        $this->assertSame([], $this->block([])->getIdentities());
        $this->assertSame([], $this->block(['search' => $this->createStub(ArgumentInterface::class)])->getIdentities());
    }

    private function block(array $data): Results
    {
        $block = (new \ReflectionClass(Results::class))->newInstanceWithoutConstructor();
        $block->setData($data);

        return $block;
    }
}
