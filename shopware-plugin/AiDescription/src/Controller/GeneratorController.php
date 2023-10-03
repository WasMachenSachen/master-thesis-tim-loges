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
        /* extract all information from the body */
        $requestData = $this->extractData($request);
        /* compose the prompts based on the requestData */
        $instructions = $this->composePrompt->composeInstructionsForGeneration($requestData['tonality']);
        $initialMessage = $this->composePrompt->composeMessageForGeneration($requestData['properties']);
        /* get the description from the OpenAI API */
        $description = $this->getDescription($instructions, $initialMessage);
        /* save the new description and then get all previous descriptions */
        $this->saveDescriptionToHistory($context, $requestData['product_id'], $requestData['tonality'], $requestData['properties'], $description, $instructions);
        $currentHistory = $this->history->readHistory($context, $requestData['product_id']);

        return new JsonResponse(['response' => $description, 'history' => $currentHistory]);
    }

    /**
     * @Route("/api/aidescription/regenerateDescription", name="api.action.ai-description.regenerateDescription", methods={"POST"}, defaults={"_routeScope"={"api"}})
     */
    public function regenerateDescription(Request $request, Context $context): JsonResponse
    {
        /* extract all information from the body */
        $requestData = $this->extractData($request);
        /* compose the prompts based on the requestData */
        $instructions = $this->composePrompt->composePromptForRephrasing($requestData['tonality']);
        /* get the description from the OpenAI API */
        $description = $this->getDescription($instructions, $requestData['description']);
        /* save the new description and then get all previous descriptions */
        $placeholderProperties = []; // we dont send the properties for the rephrasing, so for now use an empty string as a placeholder
        $this->saveDescriptionToHistory($context, $requestData['product_id'], $requestData['tonality'], $placeholderProperties, $description, $instructions);
        $currentHistory = $this->history->readHistory($context, $requestData['product_id']);

        return new JsonResponse(['response' => $description, 'history' => $currentHistory]);

    }
    /**
     * Saves the description to the history.
     *
     * @param Context $context The context object.
     * @param string $productId The product ID.
     * @param string $tonality The tonality.
     * @param array $properties The properties.
     * @param string $description The description.
     * @param string $instructions The instructions.
     */
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
    /**
     * Extracts data from the request.
     *
     * @param Request $request The request object.
     *
     * @return array The extracted data.
     */
    private function extractData(Request $request): array
    {
        $requestContent = json_decode($request->getContent());
        $tonality = strtolower($requestContent->tonality);
        $product_id = $requestContent->product_id;
        $properties = isset($requestContent->properties) ? $requestContent->properties: null;
        $description = isset($requestContent->description) ? $requestContent->description : null;

        return [
            'product_id' => $product_id,
            'tonality' => $tonality,
            'properties' => $properties,
            'description' => $description,
        ];
    }

    /**
     * Generates a description from the OpenAI API.
     *
     * @param string $instructions The instructions for the API.
     * @param string $initialMessage The initial message to send to the API.
     *
     * @return string The generated description.
     */
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
