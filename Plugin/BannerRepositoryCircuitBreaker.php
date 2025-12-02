<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

/**
 * Class BannerRepositoryCircuitBreaker
 * 
 * AROUND PLUGIN demonstrating circuit breaker / conditional execution pattern.
 * 
 * Circuit Breaker Pattern:
 * Monitor failures and prevent cascading failures by "opening the circuit"
 * when error rate exceeds threshold. This protects the system from
 * repeatedly executing operations that are likely to fail.
 * 
 * In this educational example, we implement a safety check that prevents
 * deletion of "protected" banners (e.g., banners with sort_order < 10).
 * 
 * Around plugins are ideal for this because they can:
 * 1. Check conditions BEFORE calling original method
 * 2. SKIP calling $proceed() entirely if conditions not met
 * 3. Return early without executing the original method
 * 
 * Real-World Use Cases:
 * - Rate limiting (prevent too many operations per time period)
 * - Feature flags (enable/disable features without code deploy)
 * - Maintenance mode (block operations during maintenance)
 * - Permission checks (additional authorization layer)
 * - Resource protection (prevent deletion of critical data)
 * 
 * @package Vodacom\SiteBanners\Plugin
 */
class BannerRepositoryCircuitBreaker
{
    /**
     * Banners with sort_order below this threshold are "protected"
     * and cannot be deleted (for demonstration purposes)
     */
    private const PROTECTED_SORT_ORDER_THRESHOLD = 10;

    /**
     * Constructor
     * 
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {}

    /**
     * Around plugin for delete() method - prevent deletion of protected banners
     * 
     * Circuit Breaker Pattern:
     * 1. BEFORE: Check if banner is protected
     * 2. If PROTECTED: Log warning, throw exception, SKIP $proceed()
     * 3. If ALLOWED: Call $proceed() to execute deletion
     * 4. AFTER: Log successful deletion
     * 
     * This demonstrates the power to PREVENT method execution entirely.
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param callable $proceed Closure to execute original delete()
     * @param BannerInterface $banner Banner to delete
     * @return bool True if deleted successfully
     * @throws CouldNotDeleteException If banner is protected
     */
    public function aroundDelete(
        BannerRepositoryInterface $subject,
        callable $proceed,
        BannerInterface $banner
    ): bool {
        $bannerId = $banner->getId();
        $sortOrder = $banner->getSortOrder() ?? 0;
        $title = $banner->getTitle();

        try {
            // CIRCUIT BREAKER CHECK: Is this banner protected?
            if ($this->isProtected($banner)) {
                $this->logger->warning(
                    sprintf(
                        '[CIRCUIT BREAKER] BLOCKED deletion of protected banner - ID: %d, Title: "%s", SortOrder: %d (threshold: %d)',
                        $bannerId,
                        $title,
                        $sortOrder,
                        self::PROTECTED_SORT_ORDER_THRESHOLD
                    ),
                    [
                        'banner_id' => $bannerId,
                        'sort_order' => $sortOrder,
                        'action' => 'delete_blocked'
                    ]
                );

                // CRITICAL: DO NOT call $proceed() - skip original method entirely!
                // Instead, throw exception to inform caller
                throw new CouldNotDeleteException(
                    __(
                        'Cannot delete protected banner "%1" (ID: %2). Banners with sort_order < %3 are protected.',
                        $title,
                        $bannerId,
                        self::PROTECTED_SORT_ORDER_THRESHOLD
                    )
                );
            }

            // NOT PROTECTED: Log approval and proceed with deletion
            $this->logger->info(
                sprintf(
                    '[CIRCUIT BREAKER] APPROVED deletion of banner - ID: %d, Title: "%s", SortOrder: %d',
                    $bannerId,
                    $title,
                    $sortOrder
                ),
                [
                    'banner_id' => $bannerId,
                    'sort_order' => $sortOrder,
                    'action' => 'delete_approved'
                ]
            );

            // Call original delete method
            $result = $proceed($banner);

            // Log successful deletion
            if ($result) {
                $this->logger->info(
                    sprintf(
                        '[CIRCUIT BREAKER] Banner successfully deleted after approval - ID: %d',
                        $bannerId
                    )
                );
            }

            return $result;

        } catch (CouldNotDeleteException $e) {
            // Re-throw our protection exception
            throw $e;
        } catch (\Exception $e) {
            // Log unexpected errors
            $this->logger->error(
                sprintf(
                    '[CIRCUIT BREAKER] Unexpected error in aroundDelete for banner ID %d: %s',
                    $bannerId,
                    $e->getMessage()
                )
            );
            throw $e;
        }
    }

    /**
     * Around plugin for deleteById() method - same protection logic
     * 
     * Since deleteById() internally calls getById() then delete(),
     * we need to fetch the banner first to check protection status.
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param callable $proceed Closure to execute original deleteById()
     * @param int $bannerId Banner ID to delete
     * @return bool True if deleted successfully
     * @throws CouldNotDeleteException If banner is protected or not found
     */
    public function aroundDeleteById(
        BannerRepositoryInterface $subject,
        callable $proceed,
        int $bannerId
    ): bool {
        try {
            // MUST fetch banner first to check if it's protected
            // We can use $subject (repository instance) to call other methods
            $banner = $subject->getById($bannerId);

            // Check protection status
            if ($this->isProtected($banner)) {
                $this->logger->warning(
                    sprintf(
                        '[CIRCUIT BREAKER] BLOCKED deleteById for protected banner ID: %d, SortOrder: %d',
                        $bannerId,
                        $banner->getSortOrder() ?? 0
                    )
                );

                throw new CouldNotDeleteException(
                    __(
                        'Cannot delete protected banner ID %1. Banners with sort_order < %2 are protected.',
                        $bannerId,
                        self::PROTECTED_SORT_ORDER_THRESHOLD
                    )
                );
            }

            // Approved - proceed with deletion
            $this->logger->info(
                sprintf('[CIRCUIT BREAKER] APPROVED deleteById for banner ID: %d', $bannerId)
            );

            $result = $proceed($bannerId);

            if ($result) {
                $this->logger->info(
                    sprintf('[CIRCUIT BREAKER] Banner ID %d successfully deleted after approval', $bannerId)
                );
            }

            return $result;

        } catch (CouldNotDeleteException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    '[CIRCUIT BREAKER] Error in aroundDeleteById for ID %d: %s',
                    $bannerId,
                    $e->getMessage()
                )
            );
            throw $e;
        }
    }

    /**
     * Check if banner is protected from deletion
     * 
     * Protection Rules (educational example):
     * - Banners with sort_order < 10 are considered "system banners" and protected
     * - In real applications, protection could be based on:
     *   - User permissions
     *   - Banner status (e.g., "published" banners need approval)
     *   - Time restrictions (can't delete during peak hours)
     *   - Rate limiting (max deletions per hour)
     * 
     * @param BannerInterface $banner Banner to check
     * @return bool True if protected, false if can be deleted
     */
    private function isProtected(BannerInterface $banner): bool
    {
        $sortOrder = $banner->getSortOrder() ?? 0;
        return $sortOrder < self::PROTECTED_SORT_ORDER_THRESHOLD;
    }

    /**
     * Update protection threshold (for testing/configuration)
     * 
     * In production, this would be configurable via admin panel.
     * Demonstrates that around plugins can have configuration methods.
     * 
     * Note: This is a class constant in this example, but could be
     * injected via constructor for runtime configuration.
     * 
     * @return int Current protection threshold
     */
    public function getProtectionThreshold(): int
    {
        return self::PROTECTED_SORT_ORDER_THRESHOLD;
    }
}
