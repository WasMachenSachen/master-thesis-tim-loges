<?php declare(strict_types=1);

namespace Shopware\Core\System\DeliveryTime\Aggregate\DeliveryTimeTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<DeliveryTimeTranslationEntity>
 */
#[Package('customer-order')]
class DeliveryTimeTranslationCollection extends EntityCollection
{
    /**
     * @return list<string>
     */
    public function getDeliveryTimeIds(): array
    {
        return $this->fmap(function (DeliveryTimeTranslationEntity $deliveryTimeTranslation) {
            return $deliveryTimeTranslation->getDeliveryTimeId();
        });
    }

    public function filterByDeliveryTimeId(string $id): self
    {
        return $this->filter(function (DeliveryTimeTranslationEntity $deliveryTimeTranslation) use ($id) {
            return $deliveryTimeTranslation->getDeliveryTimeId() === $id;
        });
    }

    /**
     * @return list<string>
     */
    public function getLanguageIds(): array
    {
        return $this->fmap(function (DeliveryTimeTranslationEntity $deliveryTimeTranslation) {
            return $deliveryTimeTranslation->getLanguageId();
        });
    }

    public function filterByLanguageId(string $id): self
    {
        return $this->filter(function (DeliveryTimeTranslationEntity $deliveryTimeTranslation) use ($id) {
            return $deliveryTimeTranslation->getLanguageId() === $id;
        });
    }

    public function getApiAlias(): string
    {
        return 'delivery_time_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return DeliveryTimeTranslationEntity::class;
    }
}
