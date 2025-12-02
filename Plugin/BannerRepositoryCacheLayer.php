<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class BannerRepositoryCacheLayer
 * 
 * AROUND PLUGIN demonstrating caching pattern.
 * 
 * Around plugins are the most powerful plugin type - they can:
 * 1. Execute code BEFORE the original method
 * 2. Control WHETHER the original method executes (via proceed())
 * 3. Execute code AFTER the original method
 * 4. Modify arguments AND return values
 * 
 * This plugin adds a simple in-memory cache layer to avoid redundant database queries.
 * 
 * Use Cases:
 * - Caching layer (this example)
 * - Circuit breaker pattern
 * - Request throttling
 * - Performance monitoring
 * - Conditional execution based on feature flags
 * 
 * CRITICAL: Always call $proceed() unless you have a specific reason not to!
 * Not calling $proceed() means the original method (and remaining plugins) never execute.
 * 
 * @package Vodacom\SiteBanners\Plugin
 */
class BannerRepositoryCacheLayer
{
    /**
     * In-memory cache of loaded banners (keyed by banner_id)
     * 
     * @var array<int, BannerInterface>
     */
    private array $instanceCache = [];

    /**
     * Constructor
     * 
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Around plugin for save() method - invalidate cache after save
     * 
     * Method signature pattern for around plugins:
     * public function around{MethodName}(
     *     $subject,                    // Original class instance
     *     callable $proceed,           // Closure representing original method + remaining plugins
     *     ...$originalArguments        // Original method parameters
     * ): ReturnType {                  // MUST match original method's return type
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param callable $proceed Closure to call original save() method
     * @param BannerInterface $banner Banner to save
     * @return BannerInterface Saved banner
     */
    public function aroundSave(
        BannerRepositoryInterface $subject,
        callable $proceed,
        BannerInterface $banner
    ): BannerInterface {
        try {
            // BEFORE original method: Log cache invalidation intent
            $bannerId = $banner->getId();
            if ($bannerId && isset($this->instanceCache[$bannerId])) {
                $this->logger->info(
                    sprintf(
                        '[CACHE] Invalidating cache for banner ID: %d before save',
                        $bannerId
                    )
                );
                unset($this->instanceCache[$bannerId]);
            }

            // CALL ORIGINAL METHOD: This executes the real save() and any remaining plugins
            $result = $proceed($banner);

            // AFTER original method: Log successful save
            $this->logger->info(
                sprintf(
                    '[CACHE] Banner saved successfully - ID: %d, cache invalidated',
                    $result->getId()
                )
            );

            return $result;

        } catch (\Exception $e) {
            // Exception handling in around plugins
            $this->logger->error(
                sprintf('[CACHE] Error in aroundSave: %s', $e->getMessage())
            );
            // Re-throw to maintain original behavior
            throw $e;
        }
    }

    /**
     * Around plugin for getById() method - implement cache-aside pattern
     * 
     * Cache-Aside Pattern:
     * 1. Check if data exists in cache
     * 2. If YES (cache HIT): Return cached data without calling original method
     * 3. If NO (cache MISS): Call original method, cache the result, return it
     * 
     * This demonstrates the power of around plugins - we can SKIP the original
     * method entirely if we already have the data.
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param callable $proceed Closure to call original getById() method
     * @param int $bannerId Banner ID to fetch
     * @return BannerInterface Banner object
     * @throws NoSuchEntityException If banner not found
     */
    public function aroundGetById(
        BannerRepositoryInterface $subject,
        callable $proceed,
        int $bannerId
    ): BannerInterface {
        try {
            // CACHE CHECK: Look in instance cache first
            if (isset($this->instanceCache[$bannerId])) {
                $this->logger->info(
                    sprintf(
                        '[CACHE HIT] Banner ID %d loaded from cache (skipped database query)',
                        $bannerId
                    )
                );
                // Return cached data WITHOUT calling $proceed()
                // Original method never executes = performance win!
                return $this->instanceCache[$bannerId];
            }

            // CACHE MISS: Log and proceed to database
            $this->logger->info(
                sprintf(
                    '[CACHE MISS] Banner ID %d not in cache, loading from database',
                    $bannerId
                )
            );

            // Call original method (which queries database)
            $result = $proceed($bannerId);

            // CACHE THE RESULT: Store for next call
            $this->instanceCache[$bannerId] = $result;

            $this->logger->info(
                sprintf(
                    '[CACHE] Banner ID %d loaded and cached for subsequent requests',
                    $bannerId
                )
            );

            return $result;

        } catch (NoSuchEntityException $e) {
            // Don't cache "not found" results
            $this->logger->warning(
                sprintf('[CACHE] Banner ID %d not found in database', $bannerId)
            );
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('[CACHE] Error in aroundGetById: %s', $e->getMessage())
            );
            throw $e;
        }
    }

    /**
     * Clear the entire cache
     * 
     * This could be called by other services when bulk operations occur.
     * Demonstrates that around plugins can have additional helper methods.
     * 
     * @return void
     */
    public function clearCache(): void
    {
        $count = count($this->instanceCache);
        $this->instanceCache = [];
        $this->logger->info(
            sprintf('[CACHE] Cleared instance cache (%d banners removed)', $count)
        );
    }

    /**
     * Get cache statistics
     * 
     * @return array Cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'cached_banners' => count($this->instanceCache),
            'banner_ids' => array_keys($this->instanceCache)
        ];
    }
}
