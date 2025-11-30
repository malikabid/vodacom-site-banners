<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Vodacom\SiteBanners\Api\Data\BannerInterfaceFactory;

/**
 * Save Banner Controller
 * 
 * V4.0.2: Refactored to use Repository Pattern instead of direct ResourceModel access
 */
class Save extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_save';

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var BannerInterfaceFactory
     */
    private BannerInterfaceFactory $bannerFactory;

    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param BannerInterfaceFactory $bannerFactory
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        BannerInterfaceFactory $bannerFactory,
        BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Save banner action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            // Get ID from POST data (comes from hidden field banner_id)
            $bannerId = !empty($data['banner_id']) ? (int)$data['banner_id'] : null;
            
            try {
                /** @var BannerInterface $banner */
                if ($bannerId) {
                    // Load existing banner for update using repository
                    $banner = $this->bannerRepository->getById($bannerId);
                } else {
                    // Create new banner
                    $banner = $this->bannerFactory->create();
                    // Remove banner_id from data to let AUTO_INCREMENT work
                    unset($data['banner_id']);
                }

                // Set data and save using repository
                $banner->setData($data);
                $this->bannerRepository->save($banner);

                $this->messageManager->addSuccessMessage(__('You saved the banner.'));
                $this->dataPersistor->clear('vodacom_sitebanners_banner');

                // Check if 'Save and Continue Edit' was clicked
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $banner->getBannerId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the banner.')
                );
            }

            $this->dataPersistor->set('vodacom_sitebanners_banner', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $bannerId]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
