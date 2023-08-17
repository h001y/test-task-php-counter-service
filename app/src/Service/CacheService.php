<?php

namespace App\Service;

use Predis\Client;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class CacheService
{
    const DELAY_SEC = 7;
    private Client $slaveClient;

    public function __construct(Client $slaveClient, string $cacheDir)
    {
        $this->slaveClient = $slaveClient;
        $this->cacheDir = $cacheDir;
    }
    public function save(array $data): bool
    {

        return (bool) file_put_contents($this->getFilepath(), json_encode($data));
    }

    public function canUseFile(): bool
    {
        return time() - $this->slaveClient->get('last_modified') < CacheService::DELAY_SEC;
    }

    public function toArray(): array
    {
        $fileContents = file_get_contents($this->getFilepath());
        return json_decode($fileContents, true);
    }

    public function refreshTs(): void
    {
        $master = new Client('redis://redis-master');
        $master->set('last_modified', time());
    }

    private function getFilepath():string
    {
        $tempDirectory = $this->cacheDir;
        $filename = 'cached_data.json';
        return $tempDirectory . '/' . $filename;
    }
}
