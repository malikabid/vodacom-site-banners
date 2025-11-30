<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;

/**
 * Delete Banner Controller
 * 
 * V4.0.2: Refactored to use Repository Pattern instead of direct ResourceModel access
 */
class Delete extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_delete';

    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
        $this->bannerRepository = $bannerRepository;
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
                // Use repository to delete by ID
                $this->bannerRepository->deleteById((int)$id);
                $this->messageManager->addSuccessMessage(__('The banner has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
