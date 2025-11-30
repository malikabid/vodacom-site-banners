<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Ui\Component\MassAction\Filter;
use Vodacom\SiteBanners\Api\BannerRepositoryInterface;
use Vodacom\SiteBanners\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Mass Delete Banner Controller
 * 
 * V4.0.2: Refactored to use Repository Pattern instead of direct ResourceModel access
 */
class MassDelete extends Action
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vodacom_SiteBanners::banner_delete';

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param BannerRepositoryInterface $bannerRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Execute mass delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deletedCount = 0;

        try {
            foreach ($collection as $banner) {
                // Use repository to delete banner
                $this->bannerRepository->delete($banner);
                $deletedCount++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $deletedCount)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
