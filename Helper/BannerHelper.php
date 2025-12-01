<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class BannerHelper
 * Helper class demonstrating proper dependency injection patterns
 * 
 * @package Vodacom\SiteBanners\Helper
 */
class BannerHelper extends AbstractHelper
{
    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private FilterBuilder $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * BannerHelper constructor.
     * 
     * Demonstrates proper constructor injection:
     * - Inject interfaces (BannerRepositoryInterface) not concrete classes
     * - Use private properties with type declarations
     * - Parent context provides logger, scope config, etc.
     * 
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->dateTime = $dateTime;
        $this->logger = $context->getLogger();
    }

    /**
     * Get active banners for current date/time
     * 
     * Business logic encapsulated in helper, not in template or block
     * 
     * @return \Vodacom\SiteBanners\Api\Data\BannerInterface[]
     */
    public function getActiveBanners(): array
    {
        try {
            $currentDate = $this->dateTime->gmtDate();

            // Build search criteria using injected builders
            $this->searchCriteriaBuilder->addFilters([
                $this->filterBuilder
                    ->setField('is_active')
                    ->setValue('1')
                    ->setConditionType('eq')
                    ->create()
            ]);

            // Add date range filters (nullable fields)
            $activeFromFilter = $this->filterBuilder
                ->setField('active_from')
                ->setValue($currentDate)
                ->setConditionType('lteq')
                ->create();
            
            $activeToFilter = $this->filterBuilder
                ->setField('active_to')
                ->setValue($currentDate)
                ->setConditionType('gteq')
                ->create();

            // Sort by sort_order
            $sortOrder = $this->sortOrderBuilder
                ->setField('sort_order')
                ->setDirection('ASC')
                ->create();
            $this->searchCriteriaBuilder->addSortOrder($sortOrder);

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->bannerRepository->getList($searchCriteria);

            return $searchResults->getItems();
        } catch (\Exception $e) {
            $this->logger->error('Error fetching active banners: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get banner by ID with error handling
     * 
     * @param int $bannerId
     * @return \Vodacom\SiteBanners\Api\Data\BannerInterface|null
     */
    public function getBannerById(int $bannerId): ?\Vodacom\SiteBanners\Api\Data\BannerInterface
    {
        try {
            return $this->bannerRepository->getById($bannerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->warning("Banner not found: {$bannerId}");
            return null;
        } catch (\Exception $e) {
            $this->logger->error("Error fetching banner {$bannerId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if banner is currently active
     * 
     * @param \Vodacom\SiteBanners\Api\Data\BannerInterface $banner
     * @return bool
     */
    public function isBannerActive(\Vodacom\SiteBanners\Api\Data\BannerInterface $banner): bool
    {
        if (!$banner->getIsActive()) {
            return false;
        }

        $currentDate = $this->dateTime->gmtDate();

        $activeFrom = $banner->getActiveFrom();
        $activeTo = $banner->getActiveTo();

        // No date restrictions
        if (!$activeFrom && !$activeTo) {
            return true;
        }

        // Check date range
        if ($activeFrom && $currentDate < $activeFrom) {
            return false;
        }

        if ($activeTo && $currentDate > $activeTo) {
            return false;
        }

        return true;
    }

    /**
     * Get banner count
     * 
     * @param bool $activeOnly
     * @return int
     */
    public function getBannerCount(bool $activeOnly = false): int
    {
        try {
            if ($activeOnly) {
                $this->searchCriteriaBuilder->addFilters([
                    $this->filterBuilder
                        ->setField('is_active')
                        ->setValue('1')
                        ->setConditionType('eq')
                        ->create()
                ]);
            }

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResults = $this->bannerRepository->getList($searchCriteria);

            return $searchResults->getTotalCount();
        } catch (\Exception $e) {
            $this->logger->error('Error counting banners: ' . $e->getMessage());
            return 0;
        }
    }
}
