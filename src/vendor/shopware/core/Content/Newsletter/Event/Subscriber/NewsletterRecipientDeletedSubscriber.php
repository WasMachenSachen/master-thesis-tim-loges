<?php declare(strict_types=1);

namespace Shopware\Core\Content\Newsletter\Event\Subscriber;

use Shopware\Core\Content\Newsletter\DataAbstractionLayer\NewsletterRecipientIndexingMessage;
use Shopware\Core\Content\Newsletter\NewsletterEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - EventSubscribers will become internal in v6.5.0
 */
#[Package('customer-order')]
class NewsletterRecipientDeletedSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    /**
     * @internal
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents()
    {
        return [NewsletterEvents::NEWSLETTER_RECIPIENT_DELETED_EVENT => 'onNewsletterRecipientDeleted'];
    }

    public function onNewsletterRecipientDeleted(EntityDeletedEvent $event): void
    {
        $message = new NewsletterRecipientIndexingMessage($event->getIds(), null, $event->getContext());
        $message->setDeletedNewsletterRecipients(true);

        $this->messageBus->dispatch($message);
    }
}
