<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Index
 * 
 * Display banner listing page in admin panel.
 * This is the entry point for the Site Banners admin functionality.
 * 
 * @category  Vodacom
 * @package   Vodacom_SiteBanners
 * @version   3.0.0
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     * 
     * Checks if the current admin user has permission to view Site Banners.
     * Permission is defined in etc/acl.xml as Vodacom_SiteBanners::banners
     * 
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banners';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * Constructor
     * 
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute action based on request
     * 
     * Creates admin page with title and active menu item.
     * In V3.0.0, displays a placeholder message.
     * In V3.0.1, will display the banner grid.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vodacom_SiteBanners::banners');
        $resultPage->getConfig()->getTitle()->prepend(__('Site Banners'));
        
        return $resultPage;
    }
}
