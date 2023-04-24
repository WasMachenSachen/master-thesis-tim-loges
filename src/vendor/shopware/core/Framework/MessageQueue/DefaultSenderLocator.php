<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue;

use Shopware\Core\Framework\Log\Package;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

/**
 * @deprecated tag:v6.5.0 - reason:remove-decorator - will be removed, as we use default symfony messenger
 */
#[Package('core')]
class DefaultSenderLocator implements SendersLocatorInterface
{
    private SendersLocatorInterface $inner;

    private ?SenderInterface $defaultSender;

    private ?string $defaultSenderName;

    /**
     * @internal
     */
    public function __construct(
        SendersLocatorInterface $inner,
        ?SenderInterface $defaultSender,
        ?string $defaultSenderName
    ) {
        $this->inner = $inner;
        $this->defaultSender = $defaultSender;
        $this->defaultSenderName = $defaultSenderName;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        $foundSender = false;
        foreach ($this->inner->getSenders($envelope) as $senderAlias => $sender) {
            $foundSender = true;
            yield $senderAlias => $sender;
        }

        if (!$foundSender && $this->defaultSender !== null) {
            $senderAlias = $this->defaultSenderName ?? '0';
            yield $senderAlias => $this->defaultSender;
        }
    }
}
