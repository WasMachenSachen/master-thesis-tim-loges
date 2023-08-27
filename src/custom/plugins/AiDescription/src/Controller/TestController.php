<?php declare(strict_types=1);

namespace AiDescription\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AiDescription\Service\ReadingData;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class TestController extends AbstractController
{
    private $logger;
    private $readingData;
    private SystemConfigService $systemConfigService;

    public function __construct(LoggerInterface $logger, ReadingData $readingData, SystemConfigService $systemConfigService)
    {
        $this->logger = $logger;
        $this->readingData = $readingData;
        $this->systemConfigService = $systemConfigService;

    }
    /**
     * @Route("/api/aidescription/test", name="api.action.ai-description.example", methods={"GET"}, defaults={"_routeScope"={"api"}})
     */
    public function example(Request $request, Context $contex): JsonResponse
    {

        $products = $this->readingData->readData($contex);
        // write to log file
        $this->logger->error('This is a debug message.');
        return new JsonResponse($products->jsonSerialize());
    }
    /**
     * @Route("/api/aidescription/exludedProperties", name="api.action.ai-description.example", methods={"GET"}, defaults={"_routeScope"={"api"}})
     */
    public function exludedProperties(Request $request, Context $contex): JsonResponse
    {
        $exampleConfig = $this->systemConfigService->get('AiDescription.config.excludedProperties', null);
        return new JsonResponse(
            [
                'excludedProperties' => $exampleConfig
            ]
        );

    }
}
