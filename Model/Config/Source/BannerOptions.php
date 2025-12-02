<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class BannerOptions
 * 
 * Provides banner options for widget configuration dropdown
 * Shows list of active banners for admin selection
 * 
 * @version 7.0.0
 */
class BannerOptions implements OptionSourceInterface
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    /**
     * @var array|null
     */
    private ?array $options = null;
    
    /**
     * Constructor
     *
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        LoggerInterface $logger
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->logger = $logger;
    }
    
    /**
     * Get banner options for dropdown
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }
        
        $this->options = [
            ['value' => '', 'label' => __('-- Please Select --')]
        ];
        
        try {
            // Build search criteria for active banners
            $activeFilter = $this->filterBuilder
                ->setField('is_active')
                ->setValue(1)
                ->setConditionType('eq')
                ->create();
            
            $sortOrder = $this->sortOrderBuilder
                ->setField('sort_order')
                ->setAscendingDirection()
                ->create();
            
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilters([$activeFilter])
                ->addSortOrder($sortOrder)
                ->create();
            
            // Fetch banners
            $searchResults = $this->bannerRepository->getList($searchCriteria);
            
            foreach ($searchResults->getItems() as $banner) {
                $this->options[] = [
                    'value' => $banner->getBannerId(),
                    'label' => sprintf(
                        '%s (ID: %d)',
                        $banner->getTitle(),
                        $banner->getBannerId()
                    )
                ];
            }
            
        } catch (\Exception $e) {
            $this->logger->error(
                'Error loading banner options for widget: ' . $e->getMessage(),
                ['exception' => $e]
            );
            
            $this->options[] = [
                'value' => '',
                'label' => __('Error loading banners')
            ];
        }
        
        return $this->options;
    }
}
