<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Search\Test\Unit\Model\Source;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Zero\Search\Model\Source\Categories;
use Zero\Search\Model\Source\Pages;

/**
 * Each category and page match carries the cache tag of what it links.
 */
class TagsTest extends TestCase
{
    public function testCategoryMatchesCarryTheirCategoryTag(): void
    {
        $collection = $this->createStub(CategoryCollection::class);
        foreach (['setStoreId', 'addAttributeToSelect', 'addAttributeToFilter', 'addFieldToFilter', 'addUrlRewriteToResult', 'setOrder', 'setPageSize'] as $method) {
            $collection->method($method)->willReturnSelf();
        }
        $collection->method('getItems')->willReturn([$this->category(4, 'Bags', '1/2/4'), $this->category(12, 'Bags & Packs', '1/2/12')]);
        $factory = $this->createStub(CategoryCollectionFactory::class);
        $factory->method('create')->willReturn($collection);
        $store = $this->createStub(Store::class);
        $store->method('getRootCategoryId')->willReturn(2);
        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $items = (new Categories($factory, $storeManager))->find('bag', 1, 3);

        $this->assertSame([['cat_c_4'], ['cat_c_12']], array_column($items, 'tags'));
        $this->assertSame(['Bags', 'Bags & Packs'], array_column($items, 'name'));
    }

    public function testPageMatchesCarryTheirPageTag(): void
    {
        $collection = $this->createStub(PageCollection::class);
        foreach (['addStoreFilter', 'addFieldToFilter', 'setPageSize'] as $method) {
            $collection->method($method)->willReturnSelf();
        }
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$this->page(3, 'Bag care', 'bag-care')]));
        $factory = $this->createStub(PageCollectionFactory::class);
        $factory->method('create')->willReturn($collection);
        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturnCallback(static fn ($route, $params) => '/' . $params['_direct']);

        $this->assertSame(
            [['name' => 'Bag care', 'url' => '/bag-care', 'tags' => ['cms_p_3']]],
            (new Pages($factory, $url))->find('bag', 1, 3)
        );
    }

    private function category(int $id, string $name, string $path): Category
    {
        $category = $this->createStub(Category::class);
        $category->method('getId')->willReturn($id);
        $category->method('getName')->willReturn($name);
        $category->method('getPath')->willReturn($path);
        $category->method('getUrl')->willReturn('/c' . $id . '.html');

        return $category;
    }

    private function page(int $id, string $title, string $identifier): Page
    {
        $page = $this->createStub(Page::class);
        $page->method('getId')->willReturn($id);
        $page->method('getTitle')->willReturn($title);
        $page->method('getIdentifier')->willReturn($identifier);

        return $page;
    }
}
