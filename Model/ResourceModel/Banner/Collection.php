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
     * Get active banners ordered by sort order
     *
     * @return $this
     */
    public function getActiveBanners(): self
    {
        return $this->addActiveFilter(true)
            ->addSortOrderFilter('ASC');
    }
}
