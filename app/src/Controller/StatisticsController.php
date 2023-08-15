<?php

namespace App\Controller;

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

    public function update(string $country): JsonResponse
    {
        if (!$this->isValidCountryCode($country)) {
            return $this->json([
                'message' => ' Wrong format for country for ' . $country,
                ]);
        }
        $this->redis->incr($country);

        return $this->json(['message' => 'Statistics updated for ' . $country]);
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