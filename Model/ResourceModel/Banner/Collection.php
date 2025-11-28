<?php
declare(strict_types=1);

/**
 * Copyright © Vodacom. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vodacom\SiteBanners\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Vodacom\SiteBanners\Model\Banner as BannerModel;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Banner Collection
 *
 * Collection of banner entities with filtering and sorting capabilities
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'vodacom_sitebanners_banner_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'banner_collection';

    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(BannerModel::class, BannerResource::class);
    }

    /**
     * Filter collection by active status
     *
     * @param bool $isActive
     * @return $this
     */
    public function addActiveFilter(bool $isActive = true): self
    {
        $this->addFieldToFilter('is_active', $isActive ? 1 : 0);
        return $this;
    }

    /**
     * Add sort order to collection
     *
     * @param string $direction
     * @return $this
     */
    public function addSortOrderFilter(string $direction = 'ASC'): self
    {
        $this->setOrder('sort_order', $direction);
        return $this;
    }

    /**
     * Add active date filter to collection
     *
     * Filters banners based on active_from and active_to dates.
     * NULL values are treated as no restriction.
     *
     * @param string|null $date Date to check (defaults to current date/time)
     * @return $this
     */
    public function addActiveDateFilter(?string $date = null): self
    {
        if ($date === null) {
            $date = date('Y-m-d H:i:s');
        }

        // Banner is active if:
        // 1. active_from is NULL OR active_from <= current date
        // 2. active_to is NULL OR active_to >= current date
        $this->addFieldToFilter(
            ['active_from', 'active_from'],
            [
                ['null' => true],
                ['lteq' => $date]
            ]
        );

        $this->addFieldToFilter(
            ['active_to', 'active_to'],
            [
                ['null' => true],
                ['gteq' => $date]
            ]
        );

        return $this;
    }

    /**
     * Get active banners ordered by sort order
     *
     * This method now includes date-based filtering
     *
     * @return $this
     */
    public function getActiveBanners(): self
    {
        return $this->addActiveFilter(true)
            ->addActiveDateFilter()
            ->addSortOrderFilter('ASC');
    }
}
