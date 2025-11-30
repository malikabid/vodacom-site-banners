<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Vodacom\SiteBanners\Model\BannerFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Inline Edit Banner Controller
 */
class InlineEdit extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_save';

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

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
     * @param JsonFactory $jsonFactory
     * @param BannerFactory $bannerFactory
     * @param BannerResource $bannerResource
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        BannerFactory $bannerFactory,
        BannerResource $bannerResource
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
    }

    /**
     * Execute inline edit action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $bannerId) {
            $banner = $this->bannerFactory->create();
            
            try {
                $this->bannerResource->load($banner, $bannerId);
                if (!$banner->getId()) {
                    $messages[] = __('This banner no longer exists.');
                    $error = true;
                    continue;
                }

                $banner->setData(array_merge($banner->getData(), $postItems[$bannerId]));
                $this->bannerResource->save($banner);
            } catch (\Exception $e) {
                $messages[] = __('[Banner ID: %1] %2', $bannerId, $e->getMessage());
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
