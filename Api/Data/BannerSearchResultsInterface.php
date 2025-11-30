<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for banner search results.
 * 
 * Used by repository getList() method to return
 * paginated and filtered banner collections.
 * 
 * @api
 * @since 4.0.1
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return \Vodacom\SiteBanners\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param \Vodacom\SiteBanners\Api\Data\BannerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
