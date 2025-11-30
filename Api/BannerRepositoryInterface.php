<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Vodacom\SiteBanners\Api\Data\BannerSearchResultsInterface;

/**
 * Banner repository interface.
 * 
 * Provides CRUD operations and search functionality for banner entities.
 * This is the primary interface for managing banner data.
 * 
 * @api
 * @since 4.0.2
 */
interface BannerRepositoryInterface
{
    /**
     * Save banner.
     *
     * @param BannerInterface $banner
     * @return BannerInterface
     * @throws LocalizedException
     */
    public function save(BannerInterface $banner): BannerInterface;

    /**
     * Retrieve banner by ID.
     *
     * @param int $bannerId
     * @return BannerInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $bannerId): BannerInterface;

    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return BannerSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): BannerSearchResultsInterface;

    /**
     * Delete banner.
     *
     * @param BannerInterface $banner
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(BannerInterface $banner): bool;

    /**
     * Delete banner by ID.
     *
     * @param int $bannerId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $bannerId): bool;
}
