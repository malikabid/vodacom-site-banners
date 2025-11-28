<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

/**
 * Controller to display the example banner page.
 * Corresponds to the URL: /banners/index/view
 */
class View implements HttpGetActionInterface
{
    private ResultFactory $resultFactory;

    /**
     * @param ResultFactory $resultFactory
     */
    public function __construct(ResultFactory $resultFactory)
    {
        // ResultFactory is injected via DI to create the Page result object
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute action method
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        // The page layout handle will be 'banners_index_view'
        return $resultPage;
    }
}