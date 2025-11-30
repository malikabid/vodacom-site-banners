<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Widget\Context;
use Vodacom\SiteBanners\Model\BannerFactory;

/**
 * Class GenericButton
 * Base class for form buttons
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var BannerFactory
     */
    protected BannerFactory $bannerFactory;

    /**
     * @param Context $context
     * @param BannerFactory $bannerFactory
     */
    public function __construct(
        Context $context,
        BannerFactory $bannerFactory
    ) {
        $this->context = $context;
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * Get banner ID
     *
     * @return int|null
     */
    public function getBannerId(): ?int
    {
        $bannerId = $this->context->getRequest()->getParam('id');
        return $bannerId ? (int)$bannerId : null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
