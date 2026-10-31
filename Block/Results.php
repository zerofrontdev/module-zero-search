<?php
/**
 * Copyright © 2026 Roman Pasichnyk
 * SPDX-License-Identifier: OSL-3.0 OR AFL-3.0
 */

declare(strict_types=1);

namespace Zero\Search\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Above the search results (results.phtml): the page is full page cached
 * per query, so the categories and pages it lists tag the response and
 * saving one of them cleans it. Same rule as Zero_Core's suggest block.
 */
class Results extends Template implements IdentityInterface
{
    /**
     * The cache tags of everything shown, read after the template rendered.
     *
     * @return string[]
     */
    public function getIdentities()
    {
        $search = $this->getData('search');

        return $search instanceof IdentityInterface ? $search->getIdentities() : [];
    }
}
