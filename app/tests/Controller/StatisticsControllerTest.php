<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Redis;
use Symfony\Component\HttpFoundation\Response;
use Predis\Client;
use App\Controller\StatisticsController;

class StatisticsControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGet()
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->method('keys')->willReturn(['ru_visits', 'us_visits']);
        $clientMock->method('get')->willReturn(100);

        $controller = new StatisticsController($clientMock);
        $response = $controller->get();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $expectedResponse = [
            'ru' => 100,
            'us' => 100,
        ];
        $this->assertSame(json_encode($expectedResponse), $response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $clientMock = $this->createMock(Client::class);
        $clientMock->method('get')->willReturn(100);
        $clientMock->expects($this->once())->method('set')->with('ru', 101);
        $controller = new StatisticsController($clientMock);
        $response = $controller->update('ru');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $expectedResponse = [
            'message' => 'Statistics updated successfully',
        ];
        $this->assertSame(json_encode($expectedResponse), $response->getContent());
    }
}