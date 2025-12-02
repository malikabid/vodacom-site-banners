<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BannerRepositoryPerformanceMonitor
 * 
 * AROUND PLUGIN demonstrating performance monitoring pattern.
 * 
 * This plugin wraps the getList() method to measure execution time.
 * Around plugins are perfect for this because they execute both BEFORE
 * and AFTER the original method, allowing precise timing.
 * 
 * Use Cases:
 * - Performance profiling
 * - Slow query detection
 * - SLA monitoring
 * - Performance regression alerts
 * - Debugging production issues
 * 
 * Key Concept: Around plugins have access to timing information that
 * before/after plugins cannot easily capture together.
 * 
 * @package Vodacom\SiteBanners\Plugin
 */
class BannerRepositoryPerformanceMonitor
{
    /**
     * Threshold for slow queries (in milliseconds)
     * Queries slower than this will log WARNING instead of INFO
     */
    private const SLOW_QUERY_THRESHOLD_MS = 100;

    /**
     * Constructor
     * 
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Around plugin for getList() method - measure and log execution time
     * 
     * Performance Monitoring Pattern:
     * 1. BEFORE: Record start time
     * 2. EXECUTE: Call original method via $proceed()
     * 3. AFTER: Calculate duration and log with appropriate level
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param callable $proceed Closure to execute original getList()
     * @param SearchCriteriaInterface $searchCriteria Search criteria
     * @return BannerSearchResultsInterface Search results
     */
    public function aroundGetList(
        BannerRepositoryInterface $subject,
        callable $proceed,
        SearchCriteriaInterface $searchCriteria
    ): BannerSearchResultsInterface {
        // BEFORE: Record start time (microseconds for precision)
        $startTime = microtime(true);

        try {
            // EXECUTE: Call original method (this is where the actual work happens)
            $result = $proceed($searchCriteria);

            // AFTER: Calculate execution time
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds

            // Log performance metrics with appropriate level
            $this->logPerformance(
                duration: $duration,
                resultCount: $result->getTotalCount(),
                searchCriteria: $searchCriteria
            );

            return $result;

        } catch (\Exception $e) {
            // Still calculate duration even on error
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000;

            $this->logger->error(
                sprintf(
                    '[PERFORMANCE] Banner search FAILED after %.2f ms - Error: %s',
                    $duration,
                    $e->getMessage()
                )
            );

            // Re-throw to maintain original error behavior
            throw $e;
        }
    }

    /**
     * Log performance metrics with appropriate severity level
     * 
     * @param float $duration Execution duration in milliseconds
     * @param int $resultCount Number of results returned
     * @param SearchCriteriaInterface $searchCriteria Search criteria used
     * @return void
     */
    private function logPerformance(
        float $duration,
        int $resultCount,
        SearchCriteriaInterface $searchCriteria
    ): void {
        $pageSize = $searchCriteria->getPageSize() ?? 'unlimited';
        $currentPage = $searchCriteria->getCurrentPage() ?? 1;
        $filterCount = count($searchCriteria->getFilterGroups());

        $message = sprintf(
            '[PERFORMANCE] Banner search completed in %.2f ms - Results: %d, Page: %d, PageSize: %s, Filters: %d',
            $duration,
            $resultCount,
            $currentPage,
            $pageSize,
            $filterCount
        );

        // Context data for detailed analysis
        $context = [
            'duration_ms' => round($duration, 2),
            'result_count' => $resultCount,
            'page_size' => $pageSize,
            'current_page' => $currentPage,
            'filter_count' => $filterCount,
            'is_slow_query' => $duration > self::SLOW_QUERY_THRESHOLD_MS
        ];

        // Use WARNING level for slow queries to make them stand out
        if ($duration > self::SLOW_QUERY_THRESHOLD_MS) {
            $this->logger->warning(
                $message . ' [SLOW QUERY DETECTED]',
                $context
            );
        } else {
            $this->logger->info($message, $context);
        }
    }

    /**
     * Format search criteria for debugging
     * 
     * Helper method to create human-readable representation of search criteria.
     * Useful for detailed performance analysis.
     * 
     * @param SearchCriteriaInterface $searchCriteria
     * @return string Formatted search criteria
     */
    private function formatSearchCriteria(SearchCriteriaInterface $searchCriteria): string
    {
        $filters = [];
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $filters[] = sprintf(
                    '%s %s %s',
                    $filter->getField(),
                    $filter->getConditionType() ?? '=',
                    $filter->getValue()
                );
            }
        }

        return implode(' AND ', $filters) ?: 'No filters';
    }
}
