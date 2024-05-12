<?php

namespace Domain\Transaction\Infrastructure\Integration\Notifications\SMS;

use Infrastructure\Integration\Client\IntegrationClientFaker;
use Domain\Transaction\Infrastructure\Integration\Notifications\AdapterSMSNotification;

class SMSClientMock implements AdapterSMSNotification
{
    public function sendSMSNotification(): bool
    {
        $response = IntegrationClientFaker::make($this->mock())
            ->getClient()
            ->get('https://run.mocky.io/v3/54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6', [
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
