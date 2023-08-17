<?php

namespace App\MessageHandler;

use App\Message\UpdateStatisticMessage;
use Predis\Client;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateStatisticHandler implements MessageHandlerInterface
{
    private Client $masterClient;

    public function __construct(Client $masterClient)
    {
        $this->masterClient = $masterClient;
    }

    public function __invoke(UpdateStatisticMessage $message, array $stamps = []): void
    {
        $data = json_decode($this->masterClient->get('country_json'), true);
        $data[$message->getCountryCode()] ??= 0;
        $data[$message->getCountryCode()]++;
        $this->masterClient->set('country_json', json_encode($data));
    }
}