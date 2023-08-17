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
        $message = new UpdateStatisticMessage($country);
        if (!$this->isValidCountryCode($message->getCountryCode())) {
            return $this->json([
                'message' => ' Wrong format for country for ' . $country,
                ]);
        }
        $messageBus->dispatch($message);

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

        $statistics = json_decode($this->slaveClient->get('country_json'), true);
        if ($statistics) {
            $cache->save($statistics);
            $cache->refreshTs();
            return $this->json($statistics);
        }

        return $this->json('No uploaded data .. yet');
    }

    private function isValidCountryCode(string $countryCode): bool
    {
        return in_array($countryCode, Countries::getCountryCodes(), true);
    }
}