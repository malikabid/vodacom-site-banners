<?php
declare(strict_types=1);

namespace Vodacom\SiteBanners\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class ExpandContentFieldForPageBuilder
 * Expands content field from TEXT to MEDIUMTEXT to support Page Builder HTML
 *
 * Page Builder generates verbose HTML with styling, requiring larger storage capacity.
 * MEDIUMTEXT provides up to 16MB storage vs TEXT's 64KB.
 */
class ExpandContentFieldForPageBuilder implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private SchemaSetupInterface $schemaSetup;

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * Apply schema patch
     *
     * @return void
     */
    public function apply(): void
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();
        $tableName = $this->schemaSetup->getTable('vodacom_sitebanners_banner');

        // Modify content column to MEDIUMTEXT for Page Builder HTML storage
        $connection->modifyColumn(
            $tableName,
            'content',
            [
                'type' => Table::TYPE_TEXT,
                'length' => '16M', // MEDIUMTEXT - up to 16MB
                'nullable' => true,
                'comment' => 'Banner Content (supports Page Builder)'
            ]
        );

        $this->schemaSetup->endSetup();
    }

    /**
     * Get dependencies - requires active dates columns to exist
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [
            \Vodacom\SiteBanners\Setup\Patch\Schema\AddActiveDatesToBannerTable::class
        ];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Revert patch - restore original TEXT size
     * Note: May cause data loss if content exceeds 64KB
     *
     * @return void
     */
    public function revert(): void
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();
        $tableName = $this->schemaSetup->getTable('vodacom_sitebanners_banner');

        $connection->modifyColumn(
            $tableName,
            'content',
            [
                'type' => Table::TYPE_TEXT,
                'length' => Table::DEFAULT_TEXT_SIZE, // TEXT - 64KB
                'nullable' => true,
                'comment' => 'Banner Content'
            ]
        );

        $this->schemaSetup->endSetup();
    }
}
