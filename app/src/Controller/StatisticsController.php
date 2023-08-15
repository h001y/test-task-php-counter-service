<?php

namespace App\Controller;

use App\Message\UpdateStatisticMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Interop\Queue\Exception;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Countries;
use Predis\Client;

class StatisticsController extends AbstractController
{
    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     * @throws Exception
     */
    public function update(string $country, MessageBusInterface $messageBus): JsonResponse
    {
        if (!$this->isValidCountryCode($country)) {
            return $this->json([
                'message' => ' Wrong format for country for ' . $country,
                ]);
        }
        $message = new UpdateStatisticMessage($country);
        $messageBus->dispatch($message);

        return $this->json(['message' => 'Update task added to queue']);
    }

    public function get(): JsonResponse
    {
        $statistics = [];

        foreach ($this->redis->keys('*') as $country) {
            if (!$this->isValidCountryCode($country)) {
                continue;
            }
            $count = $this->redis->get($country) ?? 0;
            $statistics[$country] = (int)$count;
        }

        return $this->json($statistics);
    }

    private function isValidCountryCode(string $countryCode): bool
    {
        return in_array($countryCode, Countries::getCountryCodes(), true);
    }
}