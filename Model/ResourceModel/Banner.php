<?php
declare(strict_types=1);

/**
 * Copyright © Vodacom. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vodacom\SiteBanners\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Banner Resource Model
 *
 * Handles database operations for banner entity
 */
class Banner extends AbstractDb
{
    /**
     * Table name
     */
    private const TABLE_NAME = 'vodacom_sitebanners_banner';

    /**
     * Primary key field name
     */
    private const PRIMARY_KEY = 'banner_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
