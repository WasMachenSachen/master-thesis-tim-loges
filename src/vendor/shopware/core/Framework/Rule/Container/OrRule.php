<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Rule\Container;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class OrRule extends Container
{
    public function match(RuleScope $scope): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->match($scope)) {
                return true;
            }
        }

        return false;
    }

    public function getName(): string
    {
        return 'orContainer';
    }
}
