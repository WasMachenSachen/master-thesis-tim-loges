<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Event\EventData;

use Shopware\Core\Framework\Log\Package;

#[Package('business-ops')]
class MailRecipientStruct
{
    /**
     * @var array
     */
    private $recipients;

    /**
     * @var string|null
     */
    private $bcc;

    /**
     * @var string|null
     */
    private $cc;

    /**
     * @param array $recipients ['email' => 'firstName lastName']
     */
    public function __construct(array $recipients)
    {
        $this->recipients = $recipients;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setRecipients(array $recipients): void
    {
        $this->recipients = $recipients;
    }

    public function getBcc(): ?string
    {
        return $this->bcc;
    }

    public function setBcc(?string $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function getCc(): ?string
    {
        return $this->cc;
    }

    public function setCc(?string $cc): void
    {
        $this->cc = $cc;
    }
}
