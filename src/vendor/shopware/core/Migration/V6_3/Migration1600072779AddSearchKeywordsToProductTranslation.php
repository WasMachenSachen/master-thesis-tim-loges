<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Migrations will be internal in v6.5.0
 */
#[Package('core')]
class Migration1600072779AddSearchKeywordsToProductTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1600072779;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `product_translation`
            ADD COLUMN `search_keywords` JSON NULL DEFAULT NULL,
            ADD CONSTRAINT `json.product_translation.search_keywords` CHECK (JSON_VALID(`search_keywords`));
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
