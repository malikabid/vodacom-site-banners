<?php
declare(strict_types=1);

/**
 * Copyright © Vodacom. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vodacom\SiteBanners\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Add active_from and active_to columns to vodacom_sitebanners_banner table
 *
 * This patch adds date scheduling functionality to banners, allowing them to be
 * automatically activated and deactivated based on configured date ranges.
 */
class AddActiveDatesToBannerTable implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private SchemaSetupInterface $schemaSetup;

    /**
     * Constructor
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * Apply schema patch
     *
     * Adds active_from and active_to columns to enable banner scheduling
     *
     * @return void
     */
    public function apply(): void
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();
        $tableName = $this->schemaSetup->getTable('vodacom_sitebanners_banner');

        // Add active_from column
        if (!$connection->tableColumnExists($tableName, 'active_from')) {
            $connection->addColumn(
                $tableName,
                'active_from',
                [
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Active From Date/Time - Banner becomes active from this date'
                ]
            );
        }

        // Add active_to column
        if (!$connection->tableColumnExists($tableName, 'active_to')) {
            $connection->addColumn(
                $tableName,
                'active_to',
                [
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Active To Date/Time - Banner becomes inactive after this date'
                ]
            );
        }

        $this->schemaSetup->endSetup();
    }

    /**
     * Get array of patches that have to be executed prior to this
     *
     * @return array<string>
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch
     *
     * @return array<string>
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Revert patch changes
     *
     * Removes active_from and active_to columns if needed for rollback
     *
     * @return void
     */
    public function revert(): void
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();
        $tableName = $this->schemaSetup->getTable('vodacom_sitebanners_banner');

        // Remove active_to column
        if ($connection->tableColumnExists($tableName, 'active_to')) {
            $connection->dropColumn($tableName, 'active_to');
        }

        // Remove active_from column
        if ($connection->tableColumnExists($tableName, 'active_from')) {
            $connection->dropColumn($tableName, 'active_from');
        }

        $this->schemaSetup->endSetup();
    }
}
