<?php declare(strict_types=1);

namespace AiDescription\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1695217169AiDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1695217169;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        ALTER TABLE `ai_description_content`
            CHANGE `description` `content` VARCHAR(255) NOT NULL,
            DROP COLUMN `settings`,
            ADD COLUMN `used_prompt` VARCHAR(255),
            ADD COLUMN `used_configuration` VARCHAR(255),
            ADD COLUMN `used_tonality` VARCHAR(255),
            ADD COLUMN `used_attributes` VARCHAR(255);
        SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
