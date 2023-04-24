<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Event;

use Shopware\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class ProgressStartedEvent extends Event
{
    public const NAME = self::class;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $total;

    public function __construct(string $message, int $total)
    {
        $this->message = $message;
        $this->total = $total;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTotal(): int
    {
        return $this->total;
    }
}
