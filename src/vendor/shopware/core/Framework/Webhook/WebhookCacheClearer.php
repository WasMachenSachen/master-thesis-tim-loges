<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Webhook;

use Shopware\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - EventSubscribers will become internal in v6.5.0
 */
#[Package('core')]
class WebhookCacheClearer implements EventSubscriberInterface, ResetInterface
{
    private WebhookDispatcher $dispatcher;

    /**
     * @internal
     */
    public function __construct(WebhookDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'webhook.written' => 'clearWebhookCache',
            'acl_role.written' => 'clearPrivilegesCache',
        ];
    }

    /**
     * Reset can not be handled by the Dispatcher itself, as it may be in the middle of a decoration chain
     * Therefore tagging that service directly won't work
     */
    public function reset(): void
    {
        $this->clearWebhookCache();
        $this->clearPrivilegesCache();
    }

    public function clearWebhookCache(): void
    {
        $this->dispatcher->clearInternalWebhookCache();
    }

    public function clearPrivilegesCache(): void
    {
        $this->dispatcher->clearInternalPrivilegesCache();
    }
}
