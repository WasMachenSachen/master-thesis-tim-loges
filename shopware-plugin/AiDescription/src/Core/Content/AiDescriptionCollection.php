<?php declare(strict_types=1);

namespace AiDescription\Core\Content;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add(AiDescriptionEntity $entity)
 * @method void              set(string $key, AiDescriptionEntity $entity)
 * @method AiDescriptionEntity[]    getIterator()
 * @method AiDescriptionEntity[]    getElements()
 * @method AiDescriptionEntity|null get(string $key)
 * @method AiDescriptionEntity|null first()
 * @method AiDescriptionEntity|null last()
 */
class AiDescriptionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AiDescriptionEntity::class;
    }
}
