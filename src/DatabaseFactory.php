<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Appwrite\Client,
	Appwrite\Services\Databases;

class DatabaseFactory {
	protected string $project;

	private Client | null $client;
	
	public function __construct(Client $client) {
		$this->client = $client;
	}

	public function createInstance(string $project): Databases | null {
        return null;
    }
}
