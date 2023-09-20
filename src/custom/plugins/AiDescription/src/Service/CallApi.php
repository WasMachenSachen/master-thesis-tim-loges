<?php declare(strict_types=1);

namespace AiDescription\Service;

use GuzzleHttp\Client;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CallApi
{
    private SystemConfigService $systemConfigService;

    private $configuration = [
      'max_tokens' => 500,
      'temperature' => 0.4,
      'top_p'=> 0.2,
      'frequency_penalty'=> 0,
      'presence_penalty'=> 0
    ];

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function callApi(String $instructions, String $initialMessage): string
    {
        //TODO: error if no apikey
        $apikey = $this->systemConfigService->get('AiDescription.config.apikey');
        if (empty($apikey)) {
            throw new \Exception('OpenAI API Key is missing. Please add it in the plugin configuration.');
        }
        $client = new Client([
          // Base URI is used with relative requests
          'base_uri' => 'https://api.openai.com',
          'timeout'  => 50,
          'read_timeout' => 50,
      ]);
        /* create bearer authentication header  */
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apikey,
        ];
        /* create json body */
        $response = $client->post(
            '/v1/chat/completions',
            [
                'headers' => $headers,
                'json' => [
                  'model' => 'gpt-4',
                  'messages' => [
                      [
                          'role' => 'system',
                          'content' => $instructions
                      ],
                      [
                          'role' => 'user',
                          'content' => $initialMessage
                      ]
                  ],
                  ...$this->configuration
                ]
            ]
        );
        $body = (string) $response->getBody();

        return $body;
    }
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

}
