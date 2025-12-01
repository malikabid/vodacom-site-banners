<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Vodacom\SiteBanners\Helper\BannerHelper;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Banner
 * Frontend block for rendering banner content
 * 
 * Version History:
 * - V3.0.4: Page Builder content rendering with CMS filter
 * - V5.0.1: Refactored to use BannerHelper (DI pattern demonstration)
 * 
 * Key Changes in V5.0.1:
 * - Replaced CollectionFactory with BannerHelper
 * - Business logic moved to Helper
 * - Block becomes thin layer for template rendering
 */
class Banner extends Template
{
    /**
     * @var BannerHelper
     */
    private BannerHelper $bannerHelper;

    /**
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;

    /**
     * Banner constructor.
     * 
     * Before V5.0.1: Injected CollectionFactory directly
     * After V5.0.1: Inject Helper which encapsulates business logic
     * 
     * @param Context $context
     * @param BannerHelper $bannerHelper
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        BannerHelper $bannerHelper,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->bannerHelper = $bannerHelper;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * Get active banners
     * 
     * V5.0.1: Business logic moved to Helper - block is thin
     * 
     * @return \Vodacom\SiteBanners\Api\Data\BannerInterface[]
     */
    public function getActiveBanners(): array
    {
        return $this->bannerHelper->getActiveBanners();
    }

    /**
     * Process banner content through Page Builder filter
     * Renders Page Builder directives, widgets, and dynamic content
     * 
     * V3.0.4: CRITICAL METHOD for Page Builder integration
     * 
     * This method is essential for displaying Page Builder content correctly.
     * Without this filter, Page Builder HTML would render as raw HTML without:
     * - Proper styling and layout
     * - Widget rendering (e.g., product widgets, CMS blocks)
     * - Directive processing (e.g., {{media url=...}}, {{store url=...}})
     * - Dynamic content (e.g., customer-specific content)
     * 
     * Example transformations:
     * - {{media url="banner.jpg"}} → Full media URL
     * - {{widget type="..."}} → Rendered widget HTML
     * - Page Builder data-* attributes → Processed and styled
     *
     * @param string $content Raw Page Builder HTML from database
     * @return string Filtered and processed HTML ready for display
     */
    public function filterContent(string $content): string
    {
        try {
            // V3.0.4: Use CMS template filter to process Page Builder content
            // FilterProvider->getPageFilter() returns the same filter used by CMS pages
            // This ensures consistent rendering of Page Builder content across the system
            $storeId = $this->_storeManager->getStore()->getId();
            return $this->filterProvider->getPageFilter()->filter($content);
        } catch (\Exception $e) {
            // Log error and return unfiltered content as fallback
            // This prevents blank banners if filtering fails
            $this->_logger->error('Error filtering banner content: ' . $e->getMessage());
            return $content;
        }
    }

    /**
     * Get banner by ID
     * 
     * V5.0.1: Use Helper method instead of direct collection
     *
     * @param int $bannerId
     * @return \Vodacom\SiteBanners\Api\Data\BannerInterface|null
     */
    public function getBanner(int $bannerId): ?\Vodacom\SiteBanners\Api\Data\BannerInterface
    {
        return $this->bannerHelper->getBannerById($bannerId);
    }

    /**
     * Check if banner is active
     * 
     * V5.0.1: Delegate to Helper
     * 
     * @param \Vodacom\SiteBanners\Api\Data\BannerInterface $banner
     * @return bool
     */
    public function isBannerActive(\Vodacom\SiteBanners\Api\Data\BannerInterface $banner): bool
    {
        return $this->bannerHelper->isBannerActive($banner);
    }

    /**
     * Get banner count
     * 
     * V5.0.1: Delegate to Helper
     * 
     * @return int
     */
    public function getBannerCount(): int
    {
        return $this->bannerHelper->getBannerCount(true);
    }
}
