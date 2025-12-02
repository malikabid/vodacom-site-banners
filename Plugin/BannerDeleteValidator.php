<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Plugin;

use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Psr\Log\LoggerInterface;

/**
 * After plugin to validate delete operations and provide feedback
 * 
 * Demonstrates:
 * - After plugin on delete() method
 * - Validating operation success
 * - Providing post-operation logging
 * - Handling void return methods (delete returns bool)
 */
class BannerDeleteValidator
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
     * After plugin for BannerRepository::delete()
     * 
     * Validates deletion and logs the operation result
     * 
     * Note: delete() returns bool, so we must return bool
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param bool $result Success/failure from original delete()
     * @param BannerInterface $banner The banner that was deleted (for context)
     * @return bool Original result (we're just observing)
     */
    public function afterDelete(
        BannerRepositoryInterface $subject,
        bool $result,
        BannerInterface $banner
    ): bool {
        if ($result) {
            $this->logger->info(
                sprintf(
                    'Banner successfully deleted - ID: %s, Title: "%s"',
                    $banner->getId(),
                    $banner->getTitle()
                ),
                [
                    'banner_id' => $banner->getId(),
                    'title' => $banner->getTitle(),
                    'operation' => 'DELETE',
                    'success' => true
                ]
            );
        } else {
            $this->logger->warning(
                sprintf(
                    'Banner deletion returned false - ID: %s, Title: "%s"',
                    $banner->getId(),
                    $banner->getTitle()
                ),
                [
                    'banner_id' => $banner->getId(),
                    'title' => $banner->getTitle(),
                    'operation' => 'DELETE',
                    'success' => false
                ]
            );
        }
        
        // Return the original result unchanged
        return $result;
    }

    /**
     * After plugin for BannerRepository::deleteById()
     * 
     * Logs deletion by ID (we don't have banner object here)
     * 
     * @param BannerRepositoryInterface $subject Repository instance
     * @param bool $result Success/failure from original deleteById()
     * @param int $bannerId Original parameter (for context)
     * @return bool Original result
     */
    public function afterDeleteById(
        BannerRepositoryInterface $subject,
        bool $result,
        int $bannerId
    ): bool {
        if ($result) {
            $this->logger->info(
                sprintf('Banner successfully deleted by ID: %d', $bannerId),
                [
                    'banner_id' => $bannerId,
                    'operation' => 'DELETE_BY_ID',
                    'success' => true
                ]
            );
        } else {
            $this->logger->warning(
                sprintf('Banner deletion by ID returned false: %d', $bannerId),
                [
                    'banner_id' => $bannerId,
                    'operation' => 'DELETE_BY_ID',
                    'success' => false
                ]
            );
        }
        
        return $result;
    }
}
