<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart\Error;

use Shopware\Core\Framework\Log\Package;

#[Package('checkout')]
class ErrorRoute
{
    private string $key;

    private array $params;

    public function __construct(string $route, ?array $params = null)
    {
        $this->key = $route;
        $this->params = $params ?? [];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
