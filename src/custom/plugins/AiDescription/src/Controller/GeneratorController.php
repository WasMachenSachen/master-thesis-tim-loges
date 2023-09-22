<?php declare(strict_types=1);

namespace AiDescription\Controller;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AiDescription\Service\CallApi;
use AiDescription\Service\ComposePrompt;
use AiDescription\Service\HistoryService;

/**
 * @Route(defaults={"_routeScope"={"api"}})
 */
class GeneratorController extends AbstractController
{
    private $callApi;
    private $composePrompt;
    private $history;

    public function __construct(CallApi $callApi, ComposePrompt $composePrompt, HistoryService $historyService)
    {
        $this->callApi = $callApi;
        $this->composePrompt = $composePrompt;
        $this->history = $historyService;
    }
    /**
     * @Route("/api/aidescription/generateDescription", name="api.action.ai-description.generateDescription", methods={"POST"}, defaults={"_routeScope"={"api"}})
     */
    public function generateDescription(Request $request, Context $context): JsonResponse
    {
        $requestData = $this->extractData($request);
        $instructions = $this->composePrompt->composeInstructionsForGeneration($requestData['tonality']);
        $initialMessage = $this->composePrompt->composeMessageForGeneration($requestData['properties']);
        $description = $this->getDescription($instructions, $initialMessage);

        $this->saveDescriptionToHistory($context, $requestData['product_id'], $requestData['tonality'], $requestData['properties'], $description, $instructions);

        $currentHistory = $this->history->readHistory($context, $requestData['product_id']);
        return new JsonResponse(['response' => $description, 'history' => $currentHistory]);
    }

    /**
     * @Route("/api/aidescription/regenerateDescription", name="api.action.ai-description.regenerateDescription", methods={"POST"}, defaults={"_routeScope"={"api"}})
     */
    public function regenerateDescription(Request $request, Context $context): JsonResponse
    {

        $requestData = $this->extractData($request);
        $instructions = $this->composePrompt->composePromptForRephrasing($requestData['tonality']);
        try {
            $response = $this->callApi->callApi($instructions, $requestData['description']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage(), 'response' =>'Fehler beim Generieren der Beschreibung']);
        }
        $response = json_decode($response);
        $description = $response->choices[0]->message->content;
        $placeholderProperties = [];
        $this->saveDescriptionToHistory($context, $requestData['product_id'], $requestData['tonality'], $placeholderProperties, $description, $instructions);

        $currentHistory = $this->history->readHistory($context, $requestData['product_id']);

        return new JsonResponse(['response' => $description, 'history' => $currentHistory]);

    }
    private function saveDescriptionToHistory(Context $context, $productId, $tonality, $properties, $description, $instructions)
    {
        $evaluation = "";
        $used_configuration = $this->callApi->getConfiguration();
        try {
            $this->history->writeHistory($context, $productId, $description, $evaluation, $instructions, $used_configuration, $properties, $tonality);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage(), 'response' =>'Fehler beim Speichern der Beschreibung']);
        }
    }
    private function extractData(Request $request): array
    {
        $requestContent = json_decode($request->getContent());
        $tonality = strtolower($requestContent->tonality);
        $product_id = $requestContent->product_id;
        $properties = $requestContent->properties;
        $description = isset($requestContent->description) ? $requestContent->description : null;

        return [
            'product_id' => $product_id,
            'tonality' => $tonality,
            'properties' => $properties,
            'description' => $description,
        ];
    }
    private function getDescription($instructions, $initialMessage)
    {
        try {
            $response = $this->callApi->callApi($instructions, $initialMessage);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage(), 'response' =>'Fehler beim Generieren der Beschreibung']);
        }
        $response = json_decode($response);
        return $response->choices[0]->message->content;
    }
}
