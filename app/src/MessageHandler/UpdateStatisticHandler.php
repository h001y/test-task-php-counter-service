<?php

namespace App\MessageHandler;

use App\Message\UpdateStatisticMessage;
use Predis\Client;

class UpdateStatisticHandler
{
    private Client $redisClient;

    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function __invoke(UpdateStatisticMessage $message): void
    {
        $countryCode = $message->getCountryCode();

        $count = $this->redisClient->get($countryCode) ?? 0;
        $this->redisClient->set($countryCode, ++$count);
    }
}