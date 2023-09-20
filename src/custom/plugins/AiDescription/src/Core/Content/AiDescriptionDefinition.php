<?php declare(strict_types=1);

namespace AiDescription\Core\Content;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;

class AiDescriptionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'ai_description_content';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return AiDescriptionEntity::class;
    }
    public function getCollectionClass(): string
    {
        return AiDescriptionCollection::class;
    }
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
          (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
          (new StringField('description', 'description'))->addFlags(new Required()),
          (new IdField('product_id', 'product_id'))->addFlags(new Required()),
          (new StringField('evaluation', 'evaluation')),
          (new StringField('settings', 'settings')),
          new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, false),

        ]);
    }
}
