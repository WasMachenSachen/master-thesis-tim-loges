<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Update\Services;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SystemConfig\SystemConfigService;

#[Package('system-settings')]
class UpdateApiHttpClientFactory
{
    private SystemConfigService $systemConfigService;

    /**
     * @internal
     */
    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function __invoke(): Client
    {
        $config = [
            'base_uri' => $this->systemConfigService->get('core.update.apiUri'),
            'headers' => [
                'Content-Type' => 'application/json',
                'ACCEPT' => 'application/json',
            ],
        ];

        return new Client($config);
    }
}
