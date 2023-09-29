<?php declare(strict_types=1);

namespace AiDescription\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AiDescription\Service\HistoryService;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class HistoryController extends AbstractController
{
    private $history;

    public function __construct(HistoryService $historyService)
    {
        $this->history = $historyService;
    }
    /**
    * @Route("/api/aidescription/history", name="api.action.ai-description.history", methods={"POST"}, defaults={"_routeScope"={"api"}})
    */
    public function getHistory(Request $request, Context $context): JsonResponse
    {
        $requestContent = json_decode($request->getContent());
        $product_id = $requestContent->product_id;
        $currentHistory = $this->history->readHistory($context, $product_id);

        return new JsonResponse(['history' => $currentHistory]);
    }
}
