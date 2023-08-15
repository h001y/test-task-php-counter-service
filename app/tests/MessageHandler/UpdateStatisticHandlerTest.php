<?php

namespace App\Tests\MessageHandler;

use App\Message\UpdateStatisticMessage;
use App\MessageHandler\UpdateStatisticHandler;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class UpdateStatisticHandlerTest extends TestCase
{
    public function testInvoke()
    {
        $countryCode = 'us';
        $message = new UpdateStatisticMessage($countryCode);

        $redisClient = $this->createMock(Client::class);
        $redisClient->expects($this->once())
            ->method('get')
            ->with($countryCode)
            ->willReturn(0);

        $redisClient->expects($this->once())
            ->method('set')
            ->with($countryCode, 1);

        $handler = new UpdateStatisticHandler($redisClient);
        $handler($message);
    }
}