<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Script;

use Shopware\Core\Checkout\Cart\Facade\CartFacade;
use Shopware\Core\Framework\Adapter\Cache\Script\Facade\CacheInvalidatorFacade;
use Shopware\Core\Framework\DataAbstractionLayer\Facade\RepositoryFacade;
use Shopware\Core\Framework\DataAbstractionLayer\Facade\RepositoryWriterFacade;
use Shopware\Core\Framework\DataAbstractionLayer\Facade\SalesChannelRepositoryFacade;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Script\Api\ScriptResponseFactoryFacade;
use Shopware\Core\Framework\Script\Debugging\ScriptTraces;
use Shopware\Core\System\SystemConfig\Facade\SystemConfigFacade;

/**
 * @example: {# @var services \Shopware\Core\Framework\Script\ServiceStubs #}
 *
 * @method CartFacade cart()
 * @method RepositoryFacade repository()
 * @method SystemConfigFacade config()
 * @method SalesChannelRepositoryFacade store()
 * @method RepositoryWriterFacade writer()
 * @method ScriptResponseFactoryFacade response()
 * @method CacheInvalidatorFacade cache()
 */
#[Package('core')]
final class ServiceStubs
{
    private string $hook;

    private array $services = [];

    /**
     * @internal
     */
    public function __construct(string $hook)
    {
        $this->hook = $hook;
    }

    /**
     * @internal
     */
    public function __call(string $name, array $arguments): object
    {
        if (!isset($this->services[$name])) {
            throw new \RuntimeException(\sprintf('The service `%s` is not available in `%s`-hook', $name, $this->hook));
        }

        if (isset($this->services[$name]['deprecation'])) {
            ScriptTraces::addDeprecationNotice($this->services[$name]['deprecation']);
        }

        return $this->services[$name]['service'];
    }

    /**
     * @internal
     */
    public function add(string $name, object $service, ?string $deprecationNotice = null): void
    {
        if (isset($this->services[$name])) {
            throw new \RuntimeException(\sprintf('Service with name "%s" already exists', $name));
        }

        $this->services[$name]['service'] = $service;

        if ($deprecationNotice) {
            $this->services[$name]['deprecation'] = $deprecationNotice;
        }
    }

    /**
     * @internal
     */
    public function get(string $name): object
    {
        if (!isset($this->services[$name])) {
            throw new \RuntimeException(\sprintf('The service `%s` is not available in `%s`-hook', $name, $this->hook));
        }

        if (isset($this->services[$name]['deprecation'])) {
            ScriptTraces::addDeprecationNotice($this->services[$name]['deprecation']);
        }

        return $this->services[$name]['service'];
    }
}
