<?php declare(strict_types=1);

namespace AiDescription\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1695220719AiDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1695220719;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        ALTER TABLE `ai_description_content`
            MODIFY `content` VARCHAR(2000) NOT NULL,
            MODIFY `used_prompt` VARCHAR(2000),
            MODIFY `used_attributes` VARCHAR(2000);
        SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
