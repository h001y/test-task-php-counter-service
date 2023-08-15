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
        $countryCode = $message->getCountryCode();
        $count = $this->masterClient->get($countryCode) ?? 0;
        $this->masterClient->set($countryCode, ++$count);
//        $this->redisClient->incr($countryCode);
    }
}