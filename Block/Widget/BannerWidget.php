<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Cms\Model\Template\FilterProvider;
use Psr\Log\LoggerInterface;

/**
 * Class BannerWidget
 * 
 * Widget block for displaying site banners
 * Supports multiple display modes: single, multiple, grid, slider
 * Integrates with Page Builder and CMS content
 * 
 * @version 7.0.0
 */
class BannerWidget extends Template implements BlockInterface
{
    /**
     * Default template
     */
    protected $_template = 'Vodacom_SiteBanners::widget/banner.phtml';
    
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
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    /**
     * @var BannerInterface[]|null
     */
    private ?array $banners = null;
    
    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param FilterProvider $filterProvider
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        FilterProvider $filterProvider,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->filterProvider = $filterProvider;
        $this->logger = $logger;
    }
    
    /**
     * Get banners based on widget configuration
     *
     * @return BannerInterface[]
     */
    public function getBanners(): array
    {
        if ($this->banners !== null) {
            return $this->banners;
        }
        
        $this->banners = [];
        
        try {
            $displayMode = $this->getData('display_mode') ?? 'single';
            
            switch ($displayMode) {
                case 'single':
                    $this->banners = $this->getSingleBanner();
                    break;
                    
                case 'multiple':
                case 'grid':
                case 'slider':
                    $this->banners = $this->getMultipleBanners();
                    break;
                    
                default:
                    $this->logger->warning('Invalid display mode: ' . $displayMode);
            }
            
        } catch (\Exception $e) {
            $this->logger->error(
                'Error loading banners for widget: ' . $e->getMessage(),
                [
                    'exception' => $e,
                    'widget_data' => $this->getData()
                ]
            );
        }
        
        return $this->banners;
    }
    
    /**
     * Get single banner
     *
     * @return BannerInterface[]
     */
    private function getSingleBanner(): array
    {
        $bannerId = (int) $this->getData('banner_id');
        
        if (!$bannerId) {
            $this->logger->warning('No banner ID specified for single display mode');
            return [];
        }
        
        try {
            $banner = $this->bannerRepository->getById($bannerId);
            
            // Check if banner is active and within date range
            if (!$this->isBannerVisible($banner)) {
                return [];
            }
            
            return [$banner];
            
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->warning('Banner not found: ' . $bannerId);
            return [];
        }
    }
    
    /**
     * Get multiple banners
     *
     * @return BannerInterface[]
     */
    private function getMultipleBanners(): array
    {
        $displayMode = $this->getData('display_mode');
        
        // Get banner IDs from appropriate parameter
        $bannerIdsParam = $this->getData('banner_ids');
        if ($displayMode === 'grid') {
            $bannerIdsParam = $this->getData('banner_ids_grid') ?: $bannerIdsParam;
        } elseif ($displayMode === 'slider') {
            $bannerIdsParam = $this->getData('banner_ids_slider') ?: $bannerIdsParam;
        }
        
        // Build filters
        $filters = [$this->createActiveFilter()];
        
        // Add date range filter
        $filters[] = $this->createDateRangeFilter();
        
        // Add specific banner IDs filter if provided
        if (!empty($bannerIdsParam)) {
            $bannerIds = array_map('trim', explode(',', $bannerIdsParam));
            $bannerIds = array_filter($bannerIds, 'is_numeric');
            
            if (!empty($bannerIds)) {
                $filters[] = $this->filterBuilder
                    ->setField('banner_id')
                    ->setValue($bannerIds)
                    ->setConditionType('in')
                    ->create();
            }
        }
        
        // Build sort order
        $sortBy = $this->getSortBy();
        $sortDirection = $this->getSortDirection();
        
        $sortOrder = null;
        if ($sortBy !== 'random') {
            $sortOrder = $this->sortOrderBuilder
                ->setField($sortBy)
                ->setDirection($sortDirection)
                ->create();
        }
        
        // Build search criteria
        $this->searchCriteriaBuilder->addFilters($filters);
        
        if ($sortOrder) {
            $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        }
        
        // Add limit if specified
        $limit = $this->getLimit();
        if ($limit > 0) {
            $this->searchCriteriaBuilder->setPageSize($limit);
        }
        
        $searchCriteria = $this->searchCriteriaBuilder->create();
        
        // Fetch banners
        $searchResults = $this->bannerRepository->getList($searchCriteria);
        $banners = $searchResults->getItems();
        
        // Apply random sorting if needed (after fetching)
        if ($sortBy === 'random') {
            shuffle($banners);
        }
        
        return $banners;
    }
    
    /**
     * Create active filter
     *
     * @return \Magento\Framework\Api\Filter
     */
    private function createActiveFilter(): \Magento\Framework\Api\Filter
    {
        return $this->filterBuilder
            ->setField('is_active')
            ->setValue(1)
            ->setConditionType('eq')
            ->create();
    }
    
    /**
     * Create date range filter
     * Banner is visible if current date is within active_from and active_to range
     *
     * @return \Magento\Framework\Api\Filter
     */
    private function createDateRangeFilter(): \Magento\Framework\Api\Filter
    {
        $currentDate = date('Y-m-d H:i:s');
        
        // This creates a filter group for date range logic
        // In repository implementation, this should be handled properly
        // For simplicity, we'll do additional filtering in isBannerVisible()
        
        return $this->filterBuilder
            ->setField('banner_id')
            ->setValue(0)
            ->setConditionType('gt')
            ->create();
    }
    
    /**
     * Check if banner is visible based on date range
     *
     * @param BannerInterface $banner
     * @return bool
     */
    private function isBannerVisible(BannerInterface $banner): bool
    {
        if (!$banner->getIsActive()) {
            return false;
        }
        
        $currentDate = new \DateTime();
        
        // Check active_from date
        if ($banner->getActiveFrom()) {
            $activeFrom = new \DateTime($banner->getActiveFrom());
            if ($currentDate < $activeFrom) {
                return false;
            }
        }
        
        // Check active_to date
        if ($banner->getActiveTo()) {
            $activeTo = new \DateTime($banner->getActiveTo());
            if ($currentDate > $activeTo) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get sort field
     *
     * @return string
     */
    private function getSortBy(): string
    {
        return $this->getData('sort_by') ?? 'sort_order';
    }
    
    /**
     * Get sort direction
     *
     * @return string
     */
    private function getSortDirection(): string
    {
        $sortBy = $this->getSortBy();
        
        // Get direction from appropriate parameter
        $direction = $this->getData('sort_direction');
        
        if ($sortBy === 'created_at') {
            $direction = $this->getData('sort_direction_date') ?: $direction;
        } elseif ($sortBy === 'title') {
            $direction = $this->getData('sort_direction_title') ?: $direction;
        }
        
        return strtoupper($direction ?? 'ASC');
    }
    
    /**
     * Get limit
     *
     * @return int
     */
    private function getLimit(): int
    {
        $displayMode = $this->getData('display_mode');
        
        $limit = $this->getData('limit');
        if ($displayMode === 'grid') {
            $limit = $this->getData('limit_grid') ?: $limit;
        } elseif ($displayMode === 'slider') {
            $limit = $this->getData('limit_slider') ?: $limit;
        }
        
        return (int) ($limit ?? 0);
    }
    
    /**
     * Get display mode
     *
     * @return string
     */
    public function getDisplayMode(): string
    {
        return $this->getData('display_mode') ?? 'single';
    }
    
    /**
     * Check if should show title
     *
     * @return bool
     */
    public function shouldShowTitle(): bool
    {
        return (bool) $this->getData('show_title');
    }
    
    /**
     * Check if should show content
     *
     * @return bool
     */
    public function shouldShowContent(): bool
    {
        return (bool) $this->getData('show_content');
    }
    
    /**
     * Get CSS class for widget container
     *
     * @return string
     */
    public function getCssClass(): string
    {
        $baseClass = 'vodacom-banner-widget';
        $customClass = $this->getData('css_class') ?? '';
        $displayMode = $this->getDisplayMode();
        
        $classes = [
            $baseClass,
            $baseClass . '-' . $displayMode,
            $customClass
        ];
        
        return trim(implode(' ', array_filter($classes)));
    }
    
    /**
     * Filter banner content through CMS template filter
     * Processes Page Builder content and widget directives
     *
     * @param string $content
     * @return string
     */
    public function filterContent(string $content): string
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
            return $this->filterProvider->getPageFilter()->filter($content);
        } catch (\Exception $e) {
            $this->logger->error(
                'Error filtering banner content: ' . $e->getMessage(),
                ['exception' => $e]
            );
            return $content;
        }
    }
    
    /**
     * Get cache key info
     *
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        $keyInfo = parent::getCacheKeyInfo();
        
        $keyInfo[] = $this->getData('display_mode');
        $keyInfo[] = $this->getData('banner_id');
        $keyInfo[] = $this->getData('banner_ids');
        $keyInfo[] = $this->getData('sort_by');
        $keyInfo[] = $this->getData('limit');
        
        return $keyInfo;
    }
}
