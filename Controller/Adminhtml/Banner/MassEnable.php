<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Vodacom\SiteBanners\Model\ResourceModel\Banner\CollectionFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Mass Enable Banner Controller
 */
class MassEnable extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_save';

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var BannerResource
     */
    private BannerResource $bannerResource;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param BannerResource $bannerResource
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BannerResource $bannerResource
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->bannerResource = $bannerResource;
    }

    /**
     * Execute mass enable action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $updatedCount = 0;

        try {
            foreach ($collection as $banner) {
                $banner->setIsActive(true);
                $this->bannerResource->save($banner);
                $updatedCount++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been enabled.', $updatedCount)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
