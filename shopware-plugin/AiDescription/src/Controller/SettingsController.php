<?php declare(strict_types=1);

namespace AiDescription\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class SettingsController extends AbstractController
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;

    }
    /**
     * @Route("/api/aidescription/exludedProperties", name="api.action.ai-description.excludedProperties", methods={"GET"}, defaults={"_routeScope"={"api"}})
     */
    public function getExludedProperties(Request $request, Context $contex): JsonResponse
    {
        $excludedProperties = $this->systemConfigService->get('AiDescription.config.excludedProperties', null);
        return new JsonResponse(
            [
                'excludedProperties' => $excludedProperties
            ]
        );

    }
}
