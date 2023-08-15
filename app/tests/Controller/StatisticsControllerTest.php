<?php

namespace App\Tests\Controller;

use App\Controller\StatisticsController;
use App\Message\UpdateStatisticMessage;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class StatisticsControllerTest extends TestCase
{
    /**
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     * @throws Exception
     */
    public function testUpdate()
    {
        $countryCode = 'us';
        $message = new UpdateStatisticMessage($countryCode);

        $redis = $this->createMock(Client::class);
        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($message));

        $controller = new StatisticsController($redis);
        $response = $controller->update($countryCode, $messageBus);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}