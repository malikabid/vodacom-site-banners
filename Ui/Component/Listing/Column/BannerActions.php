<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class BannerActions
 * 
 * Generates Edit and Delete action links for banner grid rows.
 * 
 * @category  Vodacom
 * @package   Vodacom_SiteBanners
 * @version   3.0.1
 */
class BannerActions extends Column
{
    /**
     * Edit URL path
     */
    const URL_PATH_EDIT = 'vodacom_sitebanners/banner/edit';
    
    /**
     * Delete URL path
     */
    const URL_PATH_DELETE = 'vodacom_sitebanners/banner/delete';
    
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * Adds Edit and Delete action links to each grid row.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['banner_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                ['id' => $item['banner_id']]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                ['id' => $item['banner_id']]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "%1"', $item['title']),
                                'message' => __('Are you sure you want to delete the banner "%1"?', $item['title'])
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
