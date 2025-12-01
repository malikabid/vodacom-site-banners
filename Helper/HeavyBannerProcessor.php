<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Helper;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;

/**
 * Class HeavyBannerProcessor
 * Simulates a heavy dependency that should use Proxy pattern
 * 
 * EDUCATIONAL PURPOSE:
 * This class represents an expensive operation that should NOT be instantiated
 * unless actually needed. In V5.0.2, we demonstrate Proxy pattern by:
 * 1. Creating this "heavy" class
 * 2. Injecting it via Proxy in Block/BannerStats.php
 * 3. Showing that instantiation only happens when method is called
 * 
 * REAL-WORLD ANALOGIES:
 * - Loading entire product catalog
 * - Connecting to external API
 * - Generating complex reports
 * - Processing large datasets
 * 
 * @package Vodacom\SiteBanners\Helper
 */
class HeavyBannerProcessor
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var array|null
     * Cache for processed data
     */
    private ?array $processedData = null;

    /**
     * HeavyBannerProcessor constructor.
     * 
     * IMPORTANT: This constructor simulates EXPENSIVE operations:
     * - Imagine loading 10,000 products from database
     * - Imagine connecting to external API
     * - Imagine generating complex calculations
     * 
     * That's why we inject this class via Proxy in di.xml:
     * - WITHOUT Proxy: Constructor runs immediately (expensive!)
     * - WITH Proxy: Proxy object created (cheap), real object created only when method called
     * 
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;

        // Simulate expensive initialization
        // Check var/log/system.log to see when this executes
        $this->logger->info('🔥 HeavyBannerProcessor initialized (expensive operation)');
        
        // In real-world scenario, this might be:
        // - $this->loadProductCatalog();
        // - $this->connectToExternalAPI();
        // - $this->generateComplexReport();
    }

    /**
     * Process banners with complex logic
     * 
     * Simulates CPU-intensive operation
     * In reality, this might involve:
     * - Complex calculations
     * - External API calls
     * - Large dataset processing
     * 
     * @return array
     */
    public function processBanners(): array
    {
        if ($this->processedData !== null) {
            return $this->processedData;
        }

        $this->logger->info('⚙️ Processing banners (expensive operation)...');

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->bannerRepository->getList($searchCriteria);
        $banners = $searchResults->getItems();

        $processed = [];
        foreach ($banners as $banner) {
            $processed[] = [
                'id' => $banner->getBannerId(),
                'title' => $banner->getTitle(),
                'word_count' => str_word_count(strip_tags($banner->getContent() ?? '')),
                'is_active' => $banner->getIsActive(),
                'has_dates' => $banner->getActiveFrom() || $banner->getActiveTo(),
                'content_length' => strlen($banner->getContent() ?? '')
            ];
        }

        $this->processedData = $processed;
        $this->logger->info('✅ Banner processing complete: ' . count($processed) . ' banners processed');
        
        return $processed;
    }

    /**
     * Get processing statistics
     * 
     * Demonstrates that calling ANY method on the class
     * triggers instantiation if using Proxy
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        $processed = $this->processBanners();

        return [
            'total' => count($processed),
            'active' => count(array_filter($processed, fn($b) => $b['is_active'])),
            'inactive' => count(array_filter($processed, fn($b) => !$b['is_active'])),
            'with_dates' => count(array_filter($processed, fn($b) => $b['has_dates'])),
            'avg_word_count' => count($processed) > 0 
                ? round(array_sum(array_column($processed, 'word_count')) / count($processed), 2)
                : 0,
            'avg_content_length' => count($processed) > 0 
                ? round(array_sum(array_column($processed, 'content_length')) / count($processed), 2)
                : 0
        ];
    }

    /**
     * Get detailed banner analysis
     * 
     * Another expensive operation
     * 
     * @return array
     */
    public function getDetailedAnalysis(): array
    {
        $processed = $this->processBanners();
        
        $this->logger->info('📊 Generating detailed analysis...');

        $analysis = [
            'by_status' => [
                'active' => array_filter($processed, fn($b) => $b['is_active']),
                'inactive' => array_filter($processed, fn($b) => !$b['is_active'])
            ],
            'by_dates' => [
                'scheduled' => array_filter($processed, fn($b) => $b['has_dates']),
                'permanent' => array_filter($processed, fn($b) => !$b['has_dates'])
            ],
            'content_analysis' => [
                'short' => array_filter($processed, fn($b) => $b['word_count'] < 10),
                'medium' => array_filter($processed, fn($b) => $b['word_count'] >= 10 && $b['word_count'] < 50),
                'long' => array_filter($processed, fn($b) => $b['word_count'] >= 50)
            ]
        ];

        return $analysis;
    }
}
