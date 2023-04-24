<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Plugin\Event;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
abstract class PluginLifecycleEvent extends Event
{
    /**
     * @var PluginEntity
     */
    private $plugin;

    public function __construct(PluginEntity $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(): PluginEntity
    {
        return $this->plugin;
    }
}
