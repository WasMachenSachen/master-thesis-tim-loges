<?php declare(strict_types=1);

namespace Shopware\Storefront\Controller;

use Shopware\Core\Checkout\Customer\SalesChannel\AbstractDownloadRoute;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 *
 * @internal
 */
#[Package('storefront')]
class DownloadController extends StorefrontController
{
    private AbstractDownloadRoute $downloadRoute;

    /**
     * @internal
     */
    public function __construct(AbstractDownloadRoute $downloadRoute)
    {
        $this->downloadRoute = $downloadRoute;
    }

    /**
     * @Since("6.4.19.0")
     * @Route("/account/order/download/{orderId}/{downloadId}", name="frontend.account.order.single.download", methods={"GET"})
     */
    public function downloadFile(Request $request, SalesChannelContext $context): Response
    {
        if (!$context->getCustomer()) {
            return $this->redirectToRoute(
                'frontend.account.order.single.page',
                [
                    'deepLinkCode' => $request->get('deepLinkCode', false),
                ]
            );
        }

        return $this->downloadRoute->load($request, $context);
    }
}
