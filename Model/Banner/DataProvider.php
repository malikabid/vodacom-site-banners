<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Model\Banner;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

use Vodacom\SiteBanners\Model\BannerFactory;
use Vodacom\SiteBanners\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class DataProvider
 * Provides data for the banner form UI component
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var mixed
     */
    protected $loadedData;

    /**
     * @var BannerFactory
     */
    private BannerFactory $bannerFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param BannerFactory $bannerFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        BannerFactory $bannerFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->bannerFactory = $bannerFactory;
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $bannerId = $this->request->getParam('id');

        if ($bannerId) {
            // Filter collection by banner_id and load data
            $this->collection->addFieldToFilter('banner_id', $bannerId);
            $banner = $this->collection->getFirstItem();
            
            if ($banner->getId()) {
                $this->loadedData[$banner->getId()] = $banner->getData();
            }
        }

        // Check for persisted data from DataPersistor (after validation failure)
        $data = $this->dataPersistor->get('vodacom_sitebanners_banner');
        if (!empty($data)) {
            $bannerId = isset($data['banner_id']) ? $data['banner_id'] : null;
            $this->loadedData[$bannerId] = $data;
            $this->dataPersistor->clear('vodacom_sitebanners_banner');
        }

        return $this->loadedData;
    }
}
