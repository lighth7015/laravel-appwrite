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
	protected string $project;

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
		$client = $client->setProject((
			$this->project = $project
		));
		
		$this->account = resolve( Account::class );
		$this->avatars = resolve( Avatars::class );
		$this->databases = resolve( DatabaseFactory::class );
		$this->functions = resolve( Functions::class );
		$this->health = resolve( Health::class );
		$this->locale = resolve(Locale::class );
		$this->storage = resolve(Storage::class );
		$this->teams = resolve( Teams::class );
		$this->users = resolve( Users::class );
	}

	public function account(): Account { return $this->account; }
	public function avatars(): Avatars { return $this->avatars; }
	public function factory(): DatabaseFactory { return $this->databases; }
	public function functions(): Functions { return $this->functions; }
	public function storage(): Storage { return $this->storage; }
	public function health(): Health { return $this->health; }
	public function locale(): Locale { return $this->locale; }
	public function teams(): Teams { return $this->teams; }
	public function users(): Users { return $this->users; }
}
