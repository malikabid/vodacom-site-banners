<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Psr\Log\LoggerInterface;

/**
 * Before Plugin: Log banner save operations
 * 
 * Demonstrates:
 * - Before plugin implementation
 * - Method signature: before{MethodName}
 * - Logging without modifying behavior
 * - Audit trail pattern
 * 
 * This plugin executes BEFORE BannerRepository::save()
 * to log CREATE/UPDATE operations for audit purposes.
 * 
 * Plugin Configuration:
 * - Configured in etc/di.xml
 * - sortOrder: 10 (executes after TitleSanitizer)
 * - Type: before
 * 
 * Use Cases:
 * - Audit trail / activity logging
 * - Debugging save operations
 * - Tracking who/when banners are modified
 * - Compliance and security requirements
 */
class BannerRepositorySaveLogger
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
     * Before plugin for BannerRepository::save()
     * 
     * Logs banner save operation before it happens.
     * Does NOT modify the banner or change behavior.
     * 
     * Method Signature Pattern:
     * - Must be public
     * - Method name: before + OriginalMethodName (beforeSave)
     * - First parameter: $subject (the intercepted object)
     * - Remaining parameters: same as original method
     * - Return type: void (no modification) OR array (modify parameters)
     * 
     * Plugin Execution Order:
     * 1. TitleSanitizer::beforeSave() (sortOrder 5) - modifies title
     * 2. SaveLogger::beforeSave() (sortOrder 10) - logs operation ← THIS PLUGIN
     * 3. Original method: BannerRepository::save()
     * 4. After plugins (if any)
     * 
     * @param BannerRepositoryInterface $subject The BannerRepository being intercepted
     * @param BannerInterface $banner The banner being saved
     * @return void We're not modifying parameters, just logging
     */
    public function beforeSave(
        BannerRepositoryInterface $subject,
        BannerInterface $banner
    ): void {
        // Determine if this is CREATE or UPDATE operation
        $bannerId = $banner->getBannerId();
        $operation = $bannerId ? 'UPDATE' : 'CREATE';
        
        // Build context data for logging
        $context = [
            'operation' => $operation,
            'banner_id' => $bannerId ?: 'NEW',
            'title' => $banner->getTitle(),
            'is_active' => $banner->getIsActive(),
            'sort_order' => $banner->getSortOrder(),
            'active_from' => $banner->getActiveFrom(),
            'active_to' => $banner->getActiveTo(),
        ];
        
        // Log the operation
        $this->logger->info(
            sprintf(
                'Banner %s operation initiated: "%s" (ID: %s)',
                $operation,
                $banner->getTitle(),
                $bannerId ?: 'NEW'
            ),
            $context
        );
        
        // Additional logging for specific scenarios
        if ($operation === 'CREATE') {
            $this->logger->info(
                'New banner being created',
                [
                    'title' => $banner->getTitle(),
                    'is_active' => $banner->getIsActive() ? 'Yes' : 'No'
                ]
            );
        } else {
            $this->logger->info(
                sprintf('Banner ID %s being updated', $bannerId),
                ['title' => $banner->getTitle()]
            );
        }
        
        // Log warning if banner is being deactivated
        if (!$banner->getIsActive()) {
            $this->logger->warning(
                sprintf('Banner "%s" is being deactivated', $banner->getTitle()),
                ['banner_id' => $bannerId]
            );
        }
        
        // Note: Returning void means we're NOT modifying the parameters
        // The original $banner object is passed unchanged to the next plugin/method
    }
}
