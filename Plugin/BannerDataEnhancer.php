<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * After plugin to enhance banner data with computed fields
 * 
 * Demonstrates:
 * - After plugin implementation (modifying return values)
 * - Adding computed fields to returned data
 * - Real-time data enhancement
 * - Proper return type handling
 * 
 * Use Cases:
 * - Add "days until expiration" for time-sensitive banners
 * - Add "display status" badge (Active, Scheduled, Expired)
 * - Add "last modified by" metadata
 * - Calculate engagement metrics
 */
class BannerDataEnhancer
{
    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     */
    public function __construct(
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * After plugin for BannerRepository::save()
     * 
     * Enhances saved banner with computed fields and status information
     * 
     * Method Signature Pattern:
     * - Must be public
     * - Named: after{OriginalMethodName} (afterSave, afterGetById, etc.)
     * - First param: $subject (the intercepted class instance)
     * - Second param: $result (return value from original method)
     * - Remaining params: original method parameters (optional, for context)
     * - Must return same type as original method
     * 
     * @param BannerRepositoryInterface $subject Repository instance (unused but required)
     * @param BannerInterface $result The saved banner (return value from save())
     * @param BannerInterface $banner Original banner parameter (for context/comparison)
     * @return BannerInterface Modified banner with enhanced data
     */
    public function afterSave(
        BannerRepositoryInterface $subject,
        BannerInterface $result,
        BannerInterface $banner
    ): BannerInterface {
        try {
            // Calculate display status based on dates and active flag
            $status = $this->calculateDisplayStatus($result);
            
            // Calculate days until expiration (if applicable)
            $daysRemaining = $this->calculateDaysRemaining($result);
            
            // Log enhancement for audit
            $this->logger->info(
                sprintf(
                    'Banner data enhanced after save - ID: %s, Title: "%s", Status: %s, Days Remaining: %s',
                    $result->getId() ?? 'NEW',
                    $result->getTitle(),
                    $status,
                    $daysRemaining ?? 'N/A'
                ),
                [
                    'banner_id' => $result->getId(),
                    'display_status' => $status,
                    'days_remaining' => $daysRemaining,
                    'is_active' => $result->getIsActive(),
                    'active_from' => $result->getActiveFrom(),
                    'active_to' => $result->getActiveTo()
                ]
            );
            
            // Note: In production, you'd use extension attributes to add computed fields
            // For educational purposes, we're logging the enhanced data
            
        } catch (\Exception $e) {
            // Never throw exceptions in after plugins - they break the original flow
            $this->logger->error(
                'Error enhancing banner data in after plugin: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
        
        // CRITICAL: Must return the result (banner)
        // Can modify the banner before returning if needed
        return $result;
    }

    /**
     * After plugin for BannerRepository::getById()
     * 
     * Enhances fetched banner with real-time computed fields
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param BannerInterface $result The fetched banner
     * @param int $bannerId Original parameter (for context)
     * @return BannerInterface Enhanced banner
     */
    public function afterGetById(
        BannerRepositoryInterface $subject,
        BannerInterface $result,
        int $bannerId
    ): BannerInterface {
        try {
            $status = $this->calculateDisplayStatus($result);
            $daysRemaining = $this->calculateDaysRemaining($result);
            
            $this->logger->info(
                sprintf(
                    'Banner fetched and enhanced - ID: %s, Status: %s, Days Remaining: %s',
                    $result->getId(),
                    $status,
                    $daysRemaining ?? 'N/A'
                )
            );
            
        } catch (\Exception $e) {
            $this->logger->error(
                'Error enhancing fetched banner: ' . $e->getMessage()
            );
        }
        
        return $result;
    }

    /**
     * Calculate banner display status based on dates and active flag
     * 
     * @param BannerInterface $banner
     * @return string Status: "active", "scheduled", "expired", "inactive"
     */
    private function calculateDisplayStatus(BannerInterface $banner): string
    {
        // If manually disabled, always inactive
        if (!$banner->getIsActive()) {
            return 'inactive';
        }
        
        $now = $this->dateTime->gmtTimestamp();
        $activeFrom = $banner->getActiveFrom() ? strtotime($banner->getActiveFrom()) : null;
        $activeTo = $banner->getActiveTo() ? strtotime($banner->getActiveTo()) : null;
        
        // Check if scheduled for future
        if ($activeFrom && $activeFrom > $now) {
            return 'scheduled';
        }
        
        // Check if expired
        if ($activeTo && $activeTo < $now) {
            return 'expired';
        }
        
        // Currently active
        return 'active';
    }

    /**
     * Calculate days remaining until banner expires
     * 
     * @param BannerInterface $banner
     * @return int|null Days remaining, null if no expiration date
     */
    private function calculateDaysRemaining(BannerInterface $banner): ?int
    {
        if (!$banner->getActiveTo()) {
            return null;
        }
        
        $activeTo = strtotime($banner->getActiveTo());
        $now = $this->dateTime->gmtTimestamp();
        
        // Calculate days (can be negative if expired)
        $secondsRemaining = $activeTo - $now;
        $daysRemaining = (int) ceil($secondsRemaining / 86400);
        
        return $daysRemaining;
    }
}
