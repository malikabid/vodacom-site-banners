<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Vodacom\SiteBanners\Helper\BannerHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\Escaper;
use Psr\Log\LoggerInterface;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class BannerViewModel
 * ViewModel for banner display logic
 * 
 * Key Difference from Block:
 * - Implements ArgumentInterface (not extends AbstractBlock)
 * - No rendering logic, only data preparation
 * - Highly testable (no framework dependencies in business logic)
 * - Can be injected into any template via layout XML
 * 
 * @package Vodacom\SiteBanners\ViewModel
 */
class BannerViewModel implements ArgumentInterface
{
    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @var BannerHelper
     */
    private BannerHelper $bannerHelper;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Escaper
     */
    private Escaper $escaper;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;

    /**
     * @var array|null
     * Cache for active banners
     */
    private ?array $activeBannersCache = null;

    /**
     * BannerViewModel constructor.
     * 
     * Pure dependency injection - no framework base class
     * 
     * @param BannerRepositoryInterface $bannerRepository
     * @param BannerHelper $bannerHelper
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param LoggerInterface $logger
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        BannerHelper $bannerHelper,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        LoggerInterface $logger,
        FilterProvider $filterProvider
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->bannerHelper = $bannerHelper;
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Get active banners with caching
     * 
     * Uses Helper from V5.0.1 which already implements SearchCriteria filtering
     * 
     * @return BannerInterface[]
     */
    public function getActiveBanners(): array
    {
        if ($this->activeBannersCache === null) {
            try {
                $this->activeBannersCache = $this->bannerHelper->getActiveBanners();
            } catch (\Exception $e) {
                $this->logger->error('Error fetching active banners: ' . $e->getMessage());
                $this->activeBannersCache = [];
            }
        }

        return $this->activeBannersCache;
    }

    /**
     * Check if banners exist
     * 
     * @return bool
     */
    public function hasBanners(): bool
    {
        return !empty($this->getActiveBanners());
    }

    /**
     * Get banner by ID
     * 
     * @param int $bannerId
     * @return BannerInterface|null
     */
    public function getBanner(int $bannerId): ?BannerInterface
    {
        try {
            return $this->bannerHelper->getBannerById($bannerId);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Error fetching banner ID %d: %s', $bannerId, $e->getMessage())
            );
            return null;
        }
    }

    /**
     * Get formatted banner title
     * 
     * Business logic: Escape HTML, uppercase first letter
     * 
     * @param BannerInterface $banner
     * @return string
     */
    public function getFormattedTitle(BannerInterface $banner): string
    {
        $title = $this->escaper->escapeHtml($banner->getTitle());
        return ucfirst($title);
    }

    /**
     * Get banner content (HTML safe)
     * 
     * Processes Page Builder content through CMS template filter
     * Handles: {{media url="..."}}, {{widget type="..."}}, Page Builder directives
     * 
     * @param BannerInterface $banner
     * @return string
     */
    public function getBannerContent(BannerInterface $banner): string
    {
        try {
            $content = $banner->getContent() ?? '';
            
            if (empty($content)) {
                return '';
            }
            
            // Filter through CMS template processor for Page Builder/widgets/directives
            return $this->filterProvider->getPageFilter()->filter($content);
        } catch (\Exception $e) {
            $this->logger->error('Error filtering banner content: ' . $e->getMessage());
            return $banner->getContent() ?? '';
        }
    }

    /**
     * Get banner excerpt (first N characters)
     * 
     * Business logic: Strip tags, truncate, add ellipsis
     * 
     * @param BannerInterface $banner
     * @param int $length
     * @return string
     */
    public function getBannerExcerpt(BannerInterface $banner, int $length = 100): string
    {
        $content = strip_tags($banner->getContent() ?? '');
        
        if (mb_strlen($content) <= $length) {
            return $this->escaper->escapeHtml($content);
        }

        $excerpt = mb_substr($content, 0, $length);
        $lastSpace = mb_strrpos($excerpt, ' ');
        
        if ($lastSpace !== false) {
            $excerpt = mb_substr($excerpt, 0, $lastSpace);
        }

        return $this->escaper->escapeHtml($excerpt) . '...';
    }

    /**
     * Check if banner is currently active
     * 
     * @param BannerInterface $banner
     * @return bool
     */
    public function isBannerActive(BannerInterface $banner): bool
    {
        if (!$banner->getIsActive()) {
            return false;
        }

        $now = new \DateTime();
        $activeFrom = $banner->getActiveFrom() ? new \DateTime($banner->getActiveFrom()) : null;
        $activeTo = $banner->getActiveTo() ? new \DateTime($banner->getActiveTo()) : null;

        if ($activeFrom && $now < $activeFrom) {
            return false;
        }

        if ($activeTo && $now > $activeTo) {
            return false;
        }

        return true;
    }

    /**
     * Get banner statistics
     * 
     * Uses BannerHelper's getBannerCount() method
     * 
     * @return array
     */
    public function getBannerStatistics(): array
    {
        try {
            $total = $this->bannerHelper->getBannerCount(false);
            $active = $this->bannerHelper->getBannerCount(true);
            
            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $total - $active
            ];
        } catch (\Exception $e) {
            $this->logger->error('Error fetching banner statistics: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0
            ];
        }
    }

    /**
     * Get total banner count
     * 
     * @return int
     */
    public function getTotalBannersCount(): int
    {
        try {
            return $this->bannerHelper->getBannerCount(false);
        } catch (\Exception $e) {
            $this->logger->error('Error getting total banner count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get active banner count
     * 
     * @return int
     */
    public function getActiveBannersCount(): int
    {
        return count($this->getActiveBanners());
    }

    /**
     * Get banner edit URL (for admin users)
     * 
     * @param BannerInterface $banner
     * @return string
     */
    public function getBannerEditUrl(BannerInterface $banner): string
    {
        return $this->urlBuilder->getUrl(
            'vodacom_sitebanners/banner/edit',
            ['banner_id' => $banner->getBannerId()]
        );
    }

    /**
     * Format date for display
     * 
     * @param string|null $date
     * @param string $format
     * @return string
     */
    public function formatDate(?string $date, string $format = 'M d, Y'): string
    {
        if (!$date) {
            return 'N/A';
        }

        try {
            $dateTime = new \DateTime($date);
            return $dateTime->format($format);
        } catch (\Exception $e) {
            $this->logger->warning('Invalid date format: ' . $date);
            return 'Invalid Date';
        }
    }

    /**
     * Check if banner has date restrictions
     * 
     * @param BannerInterface $banner
     * @return bool
     */
    public function hasDateRestrictions(BannerInterface $banner): bool
    {
        return $banner->getActiveFrom() !== null || $banner->getActiveTo() !== null;
    }

    /**
     * Get banner CSS classes based on status
     * 
     * Business logic for styling
     * 
     * @param BannerInterface $banner
     * @return string
     */
    public function getBannerCssClasses(BannerInterface $banner): string
    {
        $classes = ['banner-item'];

        if ($this->isBannerActive($banner)) {
            $classes[] = 'banner-active';
        } else {
            $classes[] = 'banner-inactive';
        }

        if ($this->hasDateRestrictions($banner)) {
            $classes[] = 'banner-scheduled';
        }

        return implode(' ', $classes);
    }
}
