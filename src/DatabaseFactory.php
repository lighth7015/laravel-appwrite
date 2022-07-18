<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Appwrite\Client,
	Appwrite\Services\Databases;

class DatabaseFactory {
	protected string $project;

	private Client | null $client;
	
	public function __construct(Client $client, string $project) {
		$this->client = $client->setProject((
            $this->project = $project
        ));
	}

	public function createInstance(): Databases | null {
        return null;
    }
}
