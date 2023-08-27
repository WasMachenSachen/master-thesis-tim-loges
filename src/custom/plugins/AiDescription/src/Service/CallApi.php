<?php declare(strict_types=1);

namespace AiDescription\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use GuzzleHttp\Client;

class CallApi
{
    private SystemConfigService $systemConfigService;


    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function callApi(String $pompt): string
    {
        //TODO: error if no apikey
        $apikey = $this->systemConfigService->get('AiDescription.config.apikey');
        $client = new Client([
          // Base URI is used with relative requests
          'base_uri' => 'https://api.openai.com',
          'timeout'  => 10,
          'read_timeout' => 10,
      ]);
        /* create bearer authentication header  */
        $headers = [
            'Authorization' => 'Bearer ' . $apikey,
            'Content-Type' => 'application/json',
        ];
        /* create json body */
        $response = $client->post(
            '/v1/completions',
            [
                'headers' => $headers,
                'json' => [
                  'model' => 'text-davinci-003',
                  'prompt' => $pompt,
                  'max_tokens' => 500,
                  'temperature' => 0.5,
                ]
            ]
        );
        $body = (string) $response->getBody();

        return $body;
    }

}
