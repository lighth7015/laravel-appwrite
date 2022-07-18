<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Appwrite\Client,
	Appwrite\Services\Account;

use Illuminate\Contracts\Container\Container,
	Illuminate\Support\Arr,
	Illuminate\Support\Str;

use Laravel\Lumen\Application as Lumen;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider {
	use Traits\Config;
	private static string $key = "appwrite";

	public function boot(): void {
		// @codeCoverageIgnoreStart
		if ($this->app->runningInConsole() || $this->app instanceof Lumen) {
			// @codeCoverageIgnoreEnd
			$filename = $this->filename('config/appwrite.php');
			$resolved = $this->app->configPath('appwrite.php');
			
			$this->publishes(array( $filename => $resolved ), 'config');
		}
	}

	public function register(): void {
		// @codeCoverageIgnoreStart
		if ($this->app instanceof Lumen) {
			$this->app->configure('appwrite');
		}

		// @codeCoverageIgnoreEnd
		$this->mergeConfigFrom($this->filename('config/appwrite.php'), 'appwrite');

		$this->app->bind(Client::class, function (Container $container) {
			$client = new Client;
			$selfSigned = Str::is($this->config('protocol'), 'https') && env('APP_DEBUG');

			return ($selfSigned? $client->setSelfSigned(): $client)
				->setEndpoint($this->config('endpoint'));
		});
	
		$this->app->singleton(Account::class, static fn (Container $app) => $app->make(ProjectManager::class)->project()->auth());
		$this->app->singleton(DatabaseFactory::class, static fn (Container $app) => $app->make(DatabaseFactory::class));
		
		$this->app->alias(Account::class, 'appwrite.auth');
		
		dd(resolve(Account::class));
		
		
		// $this->app->singleton(AppWrite\Contract\Database::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->database());
		// $this->app->alias(AppWrite\Contract\Database::class, 'appwrite.database');

		// $this->app->singleton(AppWrite\Contract\DynamicLinks::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->dynamicLinks());
		// $this->app->alias(AppWrite\Contract\DynamicLinks::class, 'appwrite.dynamic_links');

		// $this->app->singleton(AppWrite\Contract\Firestore::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->firestore());
		// $this->app->alias(AppWrite\Contract\Firestore::class, 'appwrite.firestore');

		// $this->app->singleton(AppWrite\Contract\Messaging::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->messaging());
		// $this->app->alias(AppWrite\Contract\Messaging::class, 'appwrite.messaging');

		// $this->app->singleton(AppWrite\Contract\RemoteConfig::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->remoteConfig());
		// $this->app->alias(AppWrite\Contract\RemoteConfig::class, 'appwrite.remote_config');

		// $this->app->singleton(AppWrite\Contract\Storage::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->storage());
		// $this->app->alias(AppWrite\Contract\Storage::class, 'appwrite.storage');
	}
}
