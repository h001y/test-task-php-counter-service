<?php

namespace App\Controller;

use App\Message\UpdateStatisticMessage;
use App\Service\CacheService;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Intl\Countries;
use Predis\Client;

class StatisticsController extends AbstractController
{
    private Client $slaveClient;


    public function __construct(Client $slaveClient)
    {
        $this->slaveClient = $slaveClient;
    }

    /**
     * @param string $country
     * @param MessageBusInterface $messageBus
     * @return JsonResponse
     */
    public function update(string $country, MessageBusInterface $messageBus): JsonResponse
    {
        $countryCode = strtoupper($country);
        if (!$this->isValidCountryCode($countryCode)) {
            return $this->json([
                'message' => ' Wrong format for country for ' . $country,
                ]);
        }
        $messageBus->dispatch(new UpdateStatisticMessage($countryCode));

        return $this->json(['message' => 'Update task added to queue']);
    }

    public function get(): JsonResponse
    {
        $cache = new CacheService(
            $this->slaveClient,
            $this->getParameter('kernel.cache_dir')
        );
        if ($cache->canUseFile()) {
            return $this->json($cache->toArray());
        }

        $statistics = $this->prepareData();
        $cache->save($statistics);
        $cache->refreshTs();
        return $this->json($statistics);
    }

    private function isValidCountryCode(string $countryCode): bool
    {
        return in_array($countryCode, Countries::getCountryCodes(), true);
    }

    private function prepareData(): array
    {
        $statistics = [];
        foreach ($this->slaveClient->keys('*') as $country) {
            if (!$this->isValidCountryCode($country)) {
                continue;
            }
            $statistics[$country] = (int)$this->slaveClient->get($country) ?? 0;
        }
        return $statistics;
    }
}