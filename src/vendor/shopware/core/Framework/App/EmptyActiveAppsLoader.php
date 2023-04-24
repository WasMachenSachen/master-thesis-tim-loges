<?php declare(strict_types=1);

namespace Shopware\Core\Framework\App;

use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.5.0 - will be removed without replacement
 */
#[Package('core')]
class EmptyActiveAppsLoader extends ActiveAppsLoader
{
    public function __construct()
    {
    }

    public function getActiveApps(): array
    {
        Feature::triggerDeprecationOrThrow('v6.5.0.0', 'EmptyActiveAppsLoader will be removed without replacement');

        return [];
    }

    public function resetActiveApps(): void
    {
        Feature::triggerDeprecationOrThrow('v6.5.0.0', 'EmptyActiveAppsLoader will be removed without replacement');
    }
}
