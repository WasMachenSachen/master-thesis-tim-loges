<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Price\Struct;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Util\FloatComparator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

#[Package('checkout')]
class AbsolutePriceDefinition extends Struct implements PriceDefinitionInterface
{
    public const TYPE = 'absolute';
    public const SORTING_PRIORITY = 75;

    /**
     * @var float
     */
    protected $price;

    /**
     * Allows to define a filter rule which line items should be considered for percentage discount/surcharge
     *
     * @var Rule|null
     */
    protected $filter;

    public function __construct(float $price, ?Rule $filter = null)
    {
        $this->price = FloatComparator::cast($price);
        $this->filter = $filter;
    }

    public function getFilter(): ?Rule
    {
        return $this->filter;
    }

    public function getPrice(): float
    {
        return FloatComparator::cast($this->price);
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getPriority(): int
    {
        return self::SORTING_PRIORITY;
    }

    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['type'] = $this->getType();

        return $data;
    }

    public static function getConstraints(): array
    {
        return [
            'price' => [new NotBlank(), new Type('numeric')],
        ];
    }

    public function getApiAlias(): string
    {
        return 'cart_price_absolute';
    }
}
