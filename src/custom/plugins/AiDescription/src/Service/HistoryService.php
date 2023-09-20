<?php declare(strict_types=1);

namespace AiDescription\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class HistoryService
{
    private EntityRepository $aiDescriptionRepository;

    public function __construct(EntityRepository $aiDescriptionRepository)
    {
        $this->aiDescriptionRepository = $aiDescriptionRepository;
    }

    /**
     * Write history function
     *
     * @param string $data
     * @return void
     */
    public function writeHistory(Context $context, string $product_id, string $content, string $evaluation, string $used_prompt, array $used_configuration, array $used_attributes, string $used_tonality): void
    {
        // remove unused informations from attributes
        $attributes = [];

        // Iterate through the original array
        foreach ($used_attributes as $item) {
            $newItem = (object)[
              "name" => $item->name,
              "value" => $item->options[0]->name,
              "checked" => $item->checked
          ];
            $attributes[] = $newItem;
        }

        $result = $this->aiDescriptionRepository->create([
            [
                      'product_id' => $product_id,
                      'content' => $content,
                      'evaluation' => $evaluation,
                      'used_prompt' => $used_prompt,
                      'used_configuration' => json_encode($used_configuration),
                      'used_attributes' => json_encode($attributes),
                      'used_tonality' => $used_tonality,

                  ]
              ], $context);
        if ($result instanceof EntityWriteResult && $result->getErrors()) {
            $errors = $result->getErrors();
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new \Exception('Failed to create history: ' . implode(', ', $errorMessages));
        }
    }

    /**
     * Read the history and return one or many
     *
     * @param string $productId
     * @param Context $context
     * @return EntitySearchResult
     */
    public function readHistory(Context $context, string $productId): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('product_id', $productId));
        $aiDescriptions = $this->aiDescriptionRepository->search($criteria, $context);
        return $aiDescriptions;
    }
    /**
    * TBD
    */
    public function updateHistroy()
    {
    }
}
