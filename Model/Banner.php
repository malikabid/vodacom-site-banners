<?php
declare(strict_types=1);

/**
 * Copyright © Vodacom. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vodacom\SiteBanners\Model;

use Magento\Framework\Model\AbstractModel;
use Vodacom\SiteBanners\Model\ResourceModel\Banner as BannerResource;

/**
 * Banner Model
 *
 * Represents a site banner entity
 */
class Banner extends AbstractModel
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
     * Get Banner ID
     *
     * @return int|null
     */
    public function getBannerId(): ?int
    {
        return $this->getData('banner_id') ? (int)$this->getData('banner_id') : null;
    }

    /**
     * Set Banner ID
     *
     * @param int $bannerId
     * @return $this
     */
    public function setBannerId(int $bannerId): self
    {
        return $this->setData('banner_id', $bannerId);
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getData('title');
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        return $this->setData('title', $title);
    }

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData('content');
    }

    /**
     * Set Content
     *
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): self
    {
        return $this->setData('content', $content);
    }

    /**
     * Get Is Active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData('is_active');
    }

    /**
     * Set Is Active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): self
    {
        return $this->setData('is_active', $isActive);
    }

    /**
     * Get Sort Order
     *
     * @return int
     */
    public function getSortOrder(): int
    {
        return (int)$this->getData('sort_order');
    }

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder(int $sortOrder): self
    {
        return $this->setData('sort_order', $sortOrder);
    }

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData('created_at');
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData('created_at', $createdAt);
    }

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData('updated_at');
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData('updated_at', $updatedAt);
    }

    /**
     * Get Active From
     *
     * @return string|null
     */
    public function getActiveFrom(): ?string
    {
        return $this->getData('active_from');
    }

    /**
     * Set Active From
     *
     * @param string|null $activeFrom
     * @return $this
     */
    public function setActiveFrom(?string $activeFrom): self
    {
        return $this->setData('active_from', $activeFrom);
    }

    /**
     * Get Active To
     *
     * @return string|null
     */
    public function getActiveTo(): ?string
    {
        return $this->getData('active_to');
    }

    /**
     * Set Active To
     *
     * @param string|null $activeTo
     * @return $this
     */
    public function setActiveTo(?string $activeTo): self
    {
        return $this->setData('active_to', $activeTo);
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
