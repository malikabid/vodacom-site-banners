<?php
declare(strict_types=1);

/**
 * Copyright © Vodacom. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vodacom\SiteBanners\Model;

use Magento\Framework\Model\AbstractModel;
use Vodacom\SiteBanners\Api\Data\BannerInterface;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Banner Model
 *
 * Represents a site banner entity.
 * Implements BannerInterface for service contract compliance.
 * 
 * @method \Vodacom\SiteBanners\Model\ResourceModel\Banner getResource()
 * @method \Vodacom\SiteBanners\Model\ResourceModel\Banner\Collection getCollection()
 */
class Banner extends AbstractModel implements BannerInterface
{
    /**
     * Cache tag for banner
     */
    public const CACHE_TAG = 'vodacom_sitebanners_banner';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var string
     */
    protected $_eventPrefix = 'vodacom_sitebanners_banner';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(BannerResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getBannerId(): ?int
    {
        $value = $this->getData(self::BANNER_ID);
        return $value !== null ? (int)$value : null;
    }

    /**
     * @inheritdoc
     */
    public function setBannerId(int $bannerId): BannerInterface
    {
        return $this->setData(self::BANNER_ID, $bannerId);
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): BannerInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritdoc
     */
    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritdoc
     */
    public function setContent(string $content): BannerInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive(): ?bool
    {
        $value = $this->getData(self::IS_ACTIVE);
        return $value !== null ? (bool)$value : null;
    }

    /**
     * @inheritdoc
     */
    public function setIsActive(bool $isActive): BannerInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder(): ?int
    {
        $value = $this->getData(self::SORT_ORDER);
        return $value !== null ? (int)$value : null;
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder(int $sortOrder): BannerInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $createdAt): BannerInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt(string $updatedAt): BannerInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getActiveFrom(): ?string
    {
        return $this->getData(self::ACTIVE_FROM);
    }

    /**
     * @inheritdoc
     */
    public function setActiveFrom(?string $activeFrom): BannerInterface
    {
        return $this->setData(self::ACTIVE_FROM, $activeFrom);
    }

    /**
     * @inheritdoc
     */
    public function getActiveTo(): ?string
    {
        return $this->getData(self::ACTIVE_TO);
    }

    /**
     * @inheritdoc
     */
    public function setActiveTo(?string $activeTo): BannerInterface
    {
        return $this->setData(self::ACTIVE_TO, $activeTo);
    }

    /**
     * Check if banner is active based on date range
     *
     * @return bool
     */
    public function isActiveByDate(): bool
    {
        $now = new \DateTime();
        
        $activeFrom = $this->getActiveFrom();
        if ($activeFrom !== null) {
            $fromDate = new \DateTime($activeFrom);
            if ($now < $fromDate) {
                return false;
            }
        }
        
        $activeTo = $this->getActiveTo();
        if ($activeTo !== null) {
            $toDate = new \DateTime($activeTo);
            if ($now > $toDate) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get identities
     *
     * @return array<string>
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
