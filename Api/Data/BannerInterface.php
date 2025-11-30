<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Api\Data;

/**
 * Banner interface.
 * 
 * Defines the contract for banner data objects.
 * Used by repository pattern and REST API.
 * 
 * @api
 * @since 4.0.1
 */
interface BannerInterface
{
    /**
     * Constants for keys of data array.
     */
    public const BANNER_ID = 'banner_id';
    public const TITLE = 'title';
    public const CONTENT = 'content';
    public const IS_ACTIVE = 'is_active';
    public const SORT_ORDER = 'sort_order';
    public const ACTIVE_FROM = 'active_from';
    public const ACTIVE_TO = 'active_to';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Get banner ID
     *
     * @return int|null
     */
    public function getBannerId(): ?int;

    /**
     * Set banner ID
     *
     * @param int $bannerId
     * @return $this
     */
    public function setBannerId(int $bannerId): self;

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self;

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self;

    /**
     * Get is active flag
     *
     * @return bool|null
     */
    public function getIsActive(): ?bool;

    /**
     * Set is active flag
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder(): ?int;

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder(int $sortOrder): self;

    /**
     * Get active from date
     *
     * @return string|null
     */
    public function getActiveFrom(): ?string;

    /**
     * Set active from date
     *
     * @param string|null $activeFrom
     * @return $this
     */
    public function setActiveFrom(?string $activeFrom): self;

    /**
     * Get active to date
     *
     * @return string|null
     */
    public function getActiveTo(): ?string;

    /**
     * Set active to date
     *
     * @param string|null $activeTo
     * @return $this
     */
    public function setActiveTo(?string $activeTo): self;

    /**
     * Get created at timestamp
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set created at timestamp
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * Get updated at timestamp
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * Set updated at timestamp
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt(string $updatedAt): self;
}
