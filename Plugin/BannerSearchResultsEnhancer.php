<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerSearchResultsInterface;
use Psr\Log\LoggerInterface;

/**
 * After plugin to enhance search results with statistics
 * 
 * Demonstrates:
 * - After plugin on getList() method (collection results)
 * - Modifying SearchResults return value
 * - Processing multiple items in result
 * - Calculating aggregate statistics
 */
class BannerSearchResultsEnhancer
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * After plugin for BannerRepository::getList()
     * 
     * Enhances search results with aggregate statistics
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param BannerSearchResultsInterface $result Search results from original method
     * @return BannerSearchResultsInterface Enhanced search results
     */
    public function afterGetList(
        BannerRepositoryInterface $subject,
        BannerSearchResultsInterface $result
    ): BannerSearchResultsInterface {
        try {
            $items = $result->getItems();
            $totalCount = $result->getTotalCount();
            
            if (empty($items)) {
                $this->logger->info('Banner search returned 0 results');
                return $result;
            }
            
            // Calculate statistics
            $stats = $this->calculateStatistics($items);
            
            // Log enhanced results
            $this->logger->info(
                sprintf(
                    'Banner search results enhanced - Total: %d, Active: %d, Scheduled: %d, Expired: %d, Inactive: %d',
                    $totalCount,
                    $stats['active'],
                    $stats['scheduled'],
                    $stats['expired'],
                    $stats['inactive']
                ),
                [
                    'total_count' => $totalCount,
                    'statistics' => $stats
                ]
            );
            
        } catch (\Exception $e) {
            $this->logger->error(
                'Error enhancing search results: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
        
        // Return the original results (or modified if needed)
        return $result;
    }

    /**
     * Calculate statistics from banner items
     * 
     * @param array $items Banner items from search results
     * @return array Statistics array
     */
    private function calculateStatistics(array $items): array
    {
        $stats = [
            'active' => 0,
            'scheduled' => 0,
            'expired' => 0,
            'inactive' => 0,
            'with_dates' => 0,
            'without_dates' => 0
        ];
        
        $now = time();
        
        foreach ($items as $banner) {
            // Count by active status
            if (!$banner->getIsActive()) {
                $stats['inactive']++;
                continue;
            }
            
            $activeFrom = $banner->getActiveFrom() ? strtotime($banner->getActiveFrom()) : null;
            $activeTo = $banner->getActiveTo() ? strtotime($banner->getActiveTo()) : null;
            
            // Check if has date restrictions
            if ($activeFrom || $activeTo) {
                $stats['with_dates']++;
            } else {
                $stats['without_dates']++;
            }
            
            // Determine status
            if ($activeFrom && $activeFrom > $now) {
                $stats['scheduled']++;
            } elseif ($activeTo && $activeTo < $now) {
                $stats['expired']++;
            } else {
                $stats['active']++;
            }
        }
        
        return $stats;
    }
}
