<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Vodacom\SiteBanners\Api\Data\BannerSearchResultsInterface;
use Vodacom\SiteBanners\Api\Data\BannerSearchResultsInterfaceFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;
use Vodacom\SiteBanners\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Banner repository implementation.
 * 
 * Provides data access layer for banner entities.
 * Abstracts direct database access behind repository interface.
 * 
 * @since 4.0.2
 */
class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @var BannerResource
     */
    private BannerResource $resource;

    /**
     * @var BannerFactory
     */
    private BannerFactory $bannerFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var BannerSearchResultsInterfaceFactory
     */
    private BannerSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @param BannerResource $resource
     * @param BannerFactory $bannerFactory
     * @param CollectionFactory $collectionFactory
     * @param BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        BannerResource $resource,
        BannerFactory $bannerFactory,
        CollectionFactory $collectionFactory,
        BannerSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->bannerFactory = $bannerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function save(BannerInterface $banner): BannerInterface
    {
        try {
            $this->resource->save($banner);
            
            // Clear cached instance if exists
            if ($banner->getBannerId()) {
                unset($this->instances[$banner->getBannerId()]);
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the banner: %1', $exception->getMessage()),
                $exception
            );
        }

        return $banner;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $bannerId): BannerInterface
    {
        // Return cached instance if available
        if (isset($this->instances[$bannerId])) {
            return $this->instances[$bannerId];
        }

        $banner = $this->bannerFactory->create();
        $this->resource->load($banner, $bannerId);

        if (!$banner->getBannerId()) {
            throw new NoSuchEntityException(
                __('Banner with id "%1" does not exist.', $bannerId)
            );
        }

        // Cache the instance
        $this->instances[$bannerId] = $banner;

        return $banner;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): BannerSearchResultsInterface
    {
        $collection = $this->collectionFactory->create();

        // Apply search criteria to collection
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var BannerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(BannerInterface $banner): bool
    {
        try {
            $bannerId = $banner->getBannerId();
            $this->resource->delete($banner);
            
            // Clear cached instance
            if ($bannerId) {
                unset($this->instances[$bannerId]);
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the banner: %1', $exception->getMessage()),
                $exception
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $bannerId): bool
    {
        return $this->delete($this->getById($bannerId));
    }
}
