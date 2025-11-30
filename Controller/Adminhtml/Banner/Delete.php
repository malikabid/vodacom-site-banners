<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Vodacom\SiteBanners\Model\BannerFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Delete Banner Controller
 */
class Delete extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_delete';

    /**
     * @var BannerFactory
     */
    private BannerFactory $bannerFactory;

    /**
     * @var BannerResource
     */
    private BannerResource $bannerResource;

    /**
     * @param Context $context
     * @param BannerFactory $bannerFactory
     * @param BannerResource $bannerResource
     */
    public function __construct(
        Context $context,
        BannerFactory $bannerFactory,
        BannerResource $bannerResource
    ) {
        parent::__construct($context);
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
    }

    /**
     * Delete banner action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $banner = $this->bannerFactory->create();
                $this->bannerResource->load($banner, $id);

                if (!$banner->getId()) {
                    $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }

                $this->bannerResource->delete($banner);
                $this->messageManager->addSuccessMessage(__('The banner has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
