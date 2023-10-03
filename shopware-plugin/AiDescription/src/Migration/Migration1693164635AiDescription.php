<?php declare(strict_types=1);

namespace AiDescription\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1693164635AiDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1693164635;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        ALTER TABLE `ai_description_content`
        ADD `created_at` DATETIME(3) NOT NULL,
        ADD `updated_at` DATETIME(3);
        SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
