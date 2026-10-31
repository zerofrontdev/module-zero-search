<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Search\Test\Unit\ViewModel;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\LayoutInterface;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Zero\Search\Model\Config;
use Zero\Search\Model\Sources;
use Zero\Search\Model\Typos;
use Zero\Search\ViewModel\Search;

/**
 * The view model's identities are the cache tags of what more() listed.
 */
class SearchTest extends TestCase
{
    public function testCategoryAndPageTagsAreCollected(): void
    {
        $found = [
            'categories' => ['title' => 'Categories', 'items' => [
                ['name' => 'Bags', 'url' => '/gear/bags.html', 'note' => 'Gear', 'tags' => ['cat_c_4']],
                ['name' => 'Bags', 'url' => '/sale/bags.html', 'tags' => ['cat_c_9', 'cat_c_4']],
            ]],
            'pages' => ['title' => 'Pages', 'items' => [
                ['name' => 'Bag care', 'url' => '/bag-care', 'tags' => ['cms_p_3', '', 7, null]],
            ]],
            // A source that tags nothing (brands, posts) adds nothing.
            'brands' => ['title' => 'Brands', 'items' => [['name' => 'Bagster', 'url' => '/brands/bagster']]],
        ];
        $search = $this->search(true, $found, 'bag');

        $this->assertSame([], $search->getIdentities(), 'nothing before more() ran');
        $this->assertSame($found, $search->more(Search::SUGGEST_LIMIT), 'the matches are returned as found');
        $this->assertSame(['cat_c_4', 'cat_c_9', 'cms_p_3'], $search->getIdentities());
    }

    public function testNoTagsWhenMoreResultsAreOff(): void
    {
        $search = $this->search(false, ['categories' => ['title' => 'Categories', 'items' => [
            ['name' => 'Bags', 'url' => '/gear/bags.html', 'tags' => ['cat_c_4']],
        ]]], 'bag');

        $this->assertSame([], $search->more(Search::RESULTS_LIMIT));
        $this->assertSame([], $search->getIdentities());
    }

    private function search(bool $on, array $found, string $query): Search
    {
        $config = $this->createStub(Config::class);
        $config->method('showsMoreResults')->willReturn($on);
        $sources = $this->createMock(Sources::class);
        $sources->expects($on ? $this->once() : $this->never())->method('find')
            ->with($query, 3, Search::SUGGEST_LIMIT)->willReturn($found);
        $queryModel = $this->createStub(Query::class);
        $queryModel->method('getQueryText')->willReturn(' ' . $query . ' ');
        $queryFactory = $this->createStub(QueryFactory::class);
        $queryFactory->method('get')->willReturn($queryModel);
        $searchHelper = $this->createStub(SearchHelper::class);
        $searchHelper->method('isMinQueryLength')->willReturn(false);
        $store = $this->createStub(Store::class);
        $store->method('getId')->willReturn(3);
        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        return new Search(
            $config,
            $sources,
            $this->createStub(Typos::class),
            $queryFactory,
            $this->createStub(QueryCollectionFactory::class),
            $searchHelper,
            $storeManager,
            $this->createStub(LayerResolver::class),
            $this->createStub(LayoutInterface::class)
        );
    }
}
