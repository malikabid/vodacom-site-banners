<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Vodacom\SiteBanners\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Banner
 * Frontend block for rendering banner content with Page Builder support
 * 
 * @since V3.0.4 - Created to support Page Builder content rendering on frontend
 * 
 * Key Features (V3.0.4):
 * - Fetches active banners from database
 * - Filters by is_active, active_from, active_to date ranges
 * - Processes Page Builder HTML through CMS template filter
 * - Handles directives, widgets, and dynamic content
 */
class Banner extends Template
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * Get active banners ordered by sort_order
     * 
     * V3.0.4: Added date range filtering with active_from/active_to
     * 
     * Filtering Logic:
     * - is_active must be 1 (enabled)
     * - active_from must be NULL (no start date) OR <= current date/time
     * - active_to must be NULL (no end date) OR >= current date/time
     * 
     * This allows banners to be:
     * - Always active (both dates NULL)
     * - Active from specific date onwards (active_from set, active_to NULL)
     * - Active until specific date (active_from NULL, active_to set)
     * - Active within date range (both dates set)
     *
     * @return \Vodacom\SiteBanners\Model\ResourceModel\Banner\Collection
     */
    public function getActiveBanners()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        
        // V3.0.4: Add date filtering if active_from/active_to are set
        $now = date('Y-m-d H:i:s');
        
        // Filter: active_from is NULL OR active_from <= now
        // Shows banners that have started or have no start date
        $collection->addFieldToFilter(
            'active_from',
            [
                ['null' => true],
                ['lteq' => $now]
            ]
        );
        
        // Filter: active_to is NULL OR active_to >= now
        // Shows banners that haven't expired or have no end date
        $collection->addFieldToFilter(
            'active_to',
            [
                ['null' => true],
                ['gteq' => $now]
            ]
        );
        
        $collection->setOrder('sort_order', 'ASC');
        
        return $collection;
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
     * @param int $bannerId
     * @return \Magento\Framework\DataObject
     */
    public function getBannerById(int $bannerId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('banner_id', ['eq' => $bannerId]);
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        
        return $collection->getFirstItem();
    }
}
