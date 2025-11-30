<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Vodacom\SiteBanners\Model\BannerFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Save Banner Controller
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
     * @var BannerFactory
     */
    private BannerFactory $bannerFactory;

    /**
     * @var BannerResource
     */
    private BannerResource $bannerResource;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param BannerFactory $bannerFactory
     * @param BannerResource $bannerResource
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        BannerFactory $bannerFactory,
        BannerResource $bannerResource
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
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
                $banner = $this->bannerFactory->create();
                
                if ($bannerId) {
                    // Load existing banner for update
                    $this->bannerResource->load($banner, $bannerId);
                    if (!$banner->getId()) {
                        $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                        return $resultRedirect->setPath('*/*/');
                    }
                    // For updates, we can keep banner_id in data since it matches the loaded banner
                } else {
                    // New banner - remove banner_id from data to let AUTO_INCREMENT work
                    unset($data['banner_id']);
                }

                // Set data and save
                $banner->setData($data);
                $this->bannerResource->save($banner);

                $this->messageManager->addSuccessMessage(__('You saved the banner.'));
                $this->dataPersistor->clear('vodacom_sitebanners_banner');

                // Check if 'Save and Continue Edit' was clicked
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $banner->getId()]);
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
