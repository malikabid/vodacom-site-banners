<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Vodacom\SiteBanners\Model\BannerFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;
use Vodacom\SiteBanners\Setup\Patch\Schema\AddActiveDatesToBannerTable;
use Psr\Log\LoggerInterface;

/**
 * Class AddSampleBanners
 * Installs 5 sample banners to demonstrate different banner configurations
 */
class AddSampleBanners implements DataPatchInterface
{
    /**
     * @var BannerFactory
     */
    private BannerFactory $bannerFactory;

    /**
     * @var BannerResource
     */
    private BannerResource $bannerResource;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param BannerFactory $bannerFactory
     * @param BannerResource $bannerResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        BannerFactory $bannerFactory,
        BannerResource $bannerResource,
        LoggerInterface $logger
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
        $this->logger = $logger;
    }

    /**
     * Install sample banner data
     *
     * @return void
     */
    public function apply(): void
    {
        $bannersData = [
            [
                'title' => 'Welcome Banner',
                'content' => 'Welcome to our store! Enjoy browsing our latest products and special offers.',
                'is_active' => 1,
                'sort_order' => 10,
                'active_from' => null,
                'active_to' => null
            ],
            [
                'title' => 'Holiday Sale 2024',
                'content' => 'Get 30% off on all items this holiday season! Limited time offer.',
                'is_active' => 1,
                'sort_order' => 20,
                'active_from' => '2024-12-01 00:00:00',
                'active_to' => '2024-12-31 23:59:59'
            ],
            [
                'title' => 'Spring Promotion 2026',
                'content' => 'Fresh new arrivals for Spring 2026. Check them out now!',
                'is_active' => 1,
                'sort_order' => 30,
                'active_from' => '2026-03-01 00:00:00',
                'active_to' => '2026-03-31 23:59:59'
            ],
            [
                'title' => 'Flash Sale - Inactive',
                'content' => 'Limited time flash sale - up to 50% off! (Currently inactive)',
                'is_active' => 0,
                'sort_order' => 40,
                'active_from' => null,
                'active_to' => null
            ],
            [
                'title' => 'Expired Limited Time Offer',
                'content' => 'Special offer that has now expired. Demonstrating past date handling.',
                'is_active' => 1,
                'sort_order' => 50,
                'active_from' => '2024-01-01 00:00:00',
                'active_to' => '2024-01-31 23:59:59'
            ]
        ];

        foreach ($bannersData as $bannerData) {
            try {
                $banner = $this->bannerFactory->create();
                $banner->setData($bannerData);
                $this->bannerResource->save($banner);
                
                $this->logger->info(
                    sprintf('Sample banner "%s" created successfully', $bannerData['title'])
                );
            } catch (\Exception $e) {
                $this->logger->error(
                    'Error creating sample banner: ' . $e->getMessage(),
                    ['banner_data' => $bannerData, 'exception' => $e]
                );
            }
        }
    }

    /**
     * Get dependencies - requires schema patch to be applied first
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [
            AddActiveDatesToBannerTable::class
        ];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Revert patch (optional - for development/testing only)
     *
     * @return void
     */
    public function revert(): void
    {
        // Optional: Delete sample banners by title
        // Not implemented as sample data is typically kept
    }
}
