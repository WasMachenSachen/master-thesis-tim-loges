<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_4;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Migrations will be internal in v6.5.0
 */
#[Package('core')]
class Migration1616555956AddPurchasePricesPropertyToProductProfile extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1616555956;
    }

    public function update(Connection $connection): void
    {
        $id = $connection->executeQuery(
            'SELECT `id` FROM `import_export_profile` WHERE `name` = :name AND `system_default` = 1',
            ['name' => 'Default product']
        )->fetchOne();
        if ($id) {
            $productMappingProfile = require __DIR__ . '/../Fixtures/import-export-profiles/ProductMappingProfile.php';
            $connection->update('import_export_profile', ['mapping' => json_encode($productMappingProfile)], ['id' => $id]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
