<?php

namespace Domain\Transaction\Infrastructure\Integration\Providers\Picpay;

use Infrastructure\Integration\Client\IntegrationClientFaker;
use Domain\Transaction\Infrastructure\Integration\Providers\AdapterProviderInterface;

class PicpayClientMock implements AdapterProviderInterface
{
    public function authorizeTransaction(): bool
    {
        $response = IntegrationClientFaker::make($this->mock())
            ->getClient()
            ->get('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc', [
                'headers' => $this->getHeader()
            ]);

        $response = json_decode($response->getBody()->__toString(), true);

        return $response['Autorizado'] == 'Autorizado';
    }

    /** @return string[] */
    private function getHeader(): array
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    /** @return string[] */
    private function mock(): array
    {
        return [
            'Autorizado' => true
        ];
    }
}
