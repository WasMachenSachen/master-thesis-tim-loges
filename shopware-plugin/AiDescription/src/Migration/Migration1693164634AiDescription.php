<?php declare(strict_types=1);

namespace AiDescription\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1693164634AiDescription extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1693164634;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
        CREATE TABLE IF NOT EXISTS `ai_description_content` (
            `id`                BINARY(16)    NOT NULL,
            `content`        VARCHAR(2000) NOT NULL,
            `product_id`       BINARY(16)    NOT NULL,
            `evaluation`    VARCHAR(255),
            `settings` VARCHAR(255),
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3),
            `used_prompt` VARCHAR(2000),
            `used_configuration` VARCHAR(255),
            `used_attributes` VARCHAR(2000),
            `used_tonality` VARCHAR(255),
            PRIMARY KEY (`id`),
            FOREIGN KEY (`product_id`) REFERENCES `product`(`id`)
        )
        ENGINE = InnoDB
        DEFAULT CHARSET = utf8mb4
        COLLATE = utf8mb4_unicode_ci;
        SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
