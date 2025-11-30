<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Edit
 * Display banner edit form
 */
class Edit extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_save';

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
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
     * Execute action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $bannerId = (int)$this->getRequest()->getParam('banner_id');
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vodacom_SiteBanners::banners');
        $resultPage->getConfig()->getTitle()->prepend(__('Site Banners'));
        
        if ($bannerId) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Banner'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Banner'));
        }
        
        return $resultPage;
    }
}
