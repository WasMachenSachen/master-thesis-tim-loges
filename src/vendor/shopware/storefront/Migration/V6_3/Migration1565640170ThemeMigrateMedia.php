<?php declare(strict_types=1);

namespace Shopware\Storefront\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Migrations will be internal in v6.5.0
 */
#[Package('storefront')]
class Migration1565640170ThemeMigrateMedia extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1565640170;
    }

    public function update(Connection $connection): void
    {
        $defaultThemeId = $connection->executeQuery(
            'SELECT `id` FROM `theme` WHERE `technical_name` = \'Storefront\';'
        )->fetchOne();

        if (!$defaultThemeId) {
            return;
        }

        $themeConfigs = $connection->executeQuery(
            'SELECT `id`, `base_config` FROM `theme`;'
        )->fetchAllAssociative();

        $themeMediaMapping = [];

        foreach ($themeConfigs as $themeConfig) {
            if (!$themeConfig['base_config']) {
                continue;
            }

            $baseConfig = json_decode($themeConfig['base_config'], true);

            if (!\array_key_exists('fields', $baseConfig) || !\is_array($baseConfig['fields'])) {
                continue;
            }

            foreach ($baseConfig['fields'] as $field) {
                if (!\array_key_exists('type', $field) || $field['type'] !== 'media') {
                    continue;
                }

                if (!\array_key_exists('value', $field) || !Uuid::isValid($field['value'])) {
                    continue;
                }

                if (\array_key_exists($field['value'], $themeMediaMapping)) {
                    continue;
                }

                $themeMediaMapping[$field['value']] = $themeConfig['id'];
            }
        }

        $mediaIds = $connection->fetchFirstColumn(
            'SELECT `media`.`id` FROM `media`
               LEFT JOIN `media_folder` ON `media`.`media_folder_id` = `media_folder`.`id`
               LEFT JOIN `media_default_folder` ON `media_folder`.`default_folder_id` = `media_default_folder`.`id`
               WHERE `media_default_folder`.`entity` = \'theme\';'
        );

        if (empty($mediaIds)) {
            return;
        }

        foreach ($mediaIds as $mediaId) {
            $connection->insert('theme_media', [
                'theme_id' => $themeMediaMapping[$mediaId] ?? $defaultThemeId,
                'media_id' => $mediaId,
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
