<?php declare(strict_types=1);

namespace AiDescription\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AiDescription\Service\ReadingData;
use AiDescription\Service\CallApi;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class GeneratorController extends AbstractController
{
    private $callApi;

    public function __construct(CallApi $callApi)
    {
        // $this->readingData = $readingData;
        // $this->systemConfigService = $systemConfigService;
        $this->callApi = $callApi;
    }
    /**
     * @Route("/api/aidescription/generateDescription", name="api.action.ai-description.generateDescription", methods={"POST"}, defaults={"_routeScope"={"api"}})
     */
    public function generateDescription(Request $request, Context $contex): JsonResponse
    {
        $requestContent = json_decode($request->getContent());
        $tonality = strtolower($requestContent->tonality);
        $composedString = '';
        foreach ($requestContent->properties as $property) {
            $composedString .= "{$property->name}: {$property->options[0]->name}.\n";
        }
        $instructions = "Du bist ein Experte für Wein und sollst eine Beschreibung für Wein erstellen. Formuliere den Text $tonality. Die Beschreibung wird in einem Onlineshop genutzt. Die Beschreibung soll nicht länger als 200 Wörter sein. Es sollen nur eine Auswahl der passenden Speisen vorkommen. Nehme die Aromen und Düfte mit in die Beschreibung auf. Hier sind die Informationen zu dem Wein:";
        $prompt = "$instructions \n$composedString";
        //TODO: what todo if error and response is no json?
        $response = $this->callApi->callApi($prompt);
        return new JsonResponse(
            [
                'generateDescription' => $response,
            ]
        );

    }
}
