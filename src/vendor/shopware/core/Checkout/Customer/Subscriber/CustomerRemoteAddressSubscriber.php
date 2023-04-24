<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Customer\Subscriber;

use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - EventSubscribers will become internal in v6.5.0
 */
#[Package('customer-order')]
class CustomerRemoteAddressSubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $customerRepository;

    private RequestStack $requestStack;

    /**
     * @internal
     */
    public function __construct(
        EntityRepositoryInterface $customerRepository,
        RequestStack $requestStack
    ) {
        $this->customerRepository = $customerRepository;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CustomerLoginEvent::class => 'updateRemoteAddressByLogin',
        ];
    }

    public function updateRemoteAddressByLogin(CustomerLoginEvent $event): void
    {
        $request = $this->requestStack
            ->getMainRequest();

        if (!$request) {
            return;
        }

        $this->customerRepository->update([
            [
                'id' => $event->getCustomer()->getId(),
                'remoteAddress' => $request->getClientIp(),
            ],
        ], $event->getContext());
    }
}
