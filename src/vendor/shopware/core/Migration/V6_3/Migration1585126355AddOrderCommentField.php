<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Migrations will be internal in v6.5.0
 */
#[Package('core')]
class Migration1585126355AddOrderCommentField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1585126355;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `order`
            ADD COLUMN `customer_comment` LONGTEXT COLLATE utf8mb4_unicode_ci NULL AFTER `campaign_code`;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
