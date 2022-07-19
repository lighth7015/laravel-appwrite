<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Appwrite\Client,
	Appwrite\Services\Account,
	Appwrite\Services\Avatars,
	Appwrite\Services\Functions,
	Appwrite\Services\Health,
	Appwrite\Services\Locale,
	Appwrite\Services\Storage,
	Appwrite\Services\Teams,
	Appwrite\Services\Users;

class Project {
	private Account | null $account;
	private Avatars | null $avatars;
	private DatabaseFactory $databases;
	private Functions | null $functions;
	private Health | null $health;
	private Locale | null $locale;
	private Storage | null $storage;
	private Teams | null $teams;
	private Users | null $users;
	
	public function __construct(Client $client, string $project) {
		$this->client = $client->setProject($project);
		
		$this->account = new Account($client);
		$this->avatars = new Avatars($client);
		$this->databases = new DatabaseFactory($client);
		$this->functions = new Functions($client);
		$this->health = new Health($client);
		$this->locale = new Locale($client);
		$this->storage = new Storage($client);
		$this->teams = new Teams($client);
		$this->users = new Users($client);
	}

	public function auth(): Users { return $this->users; }
	public function account(): Users { return $this->account; }
	public function avatars(): Avatars { return $this->avatars; }
	public function factory(string $database): DatabaseFactory {
		return $this->databases->createInstance($database);
	}
	public function functions(): Functions { return $this->functions; }
	public function storage(): Storage { return $this->storage; }
	public function health(): Health { return $this->health; }
	public function locale(): Locale { return $this->locale; }
	public function teams(): Teams { return $this->teams; }
}
