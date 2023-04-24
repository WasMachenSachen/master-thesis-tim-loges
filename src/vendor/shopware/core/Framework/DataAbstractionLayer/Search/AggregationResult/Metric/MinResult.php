<?php declare(strict_types=1);

namespace Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric;

use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shopware\Core\Framework\Log\Package;

/**
 * @final tag:v6.5.0
 */
#[Package('core')]
class MinResult extends AggregationResult
{
    /**
     * @var float|int|string|null
     */
    protected $min;

    /**
     * @param string|float|int|null $min
     */
    public function __construct(string $name, $min)
    {
        parent::__construct($name);
        $this->min = $min;
    }

    /**
     * @return float|int|string|null
     */
    public function getMin()
    {
        return $this->min;
    }
}
