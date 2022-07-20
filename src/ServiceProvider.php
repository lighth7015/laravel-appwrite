<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Appwrite\Client,
	Appwrite\Services\Users;

use Illuminate\Contracts\Container\Container,
	Illuminate\Support\Arr,
	Illuminate\Support\Str;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider {
	use Traits\Config {
		Traits\Config::config as config;
	}
	
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
		$this->mergeConfigFrom($this->filename('config/appwrite.php'), 'appwrite');

		$this->app->bind(Client::class, function (Container $container) {
			$client = new Client;
			$selfSigned = Str::is($this->config('protocol'), 'https') && env('APP_DEBUG');

			return ($selfSigned? $client->setSelfSigned(): $client)
				->setEndpoint($this->config('endpoint'));
		});
	
		$this->app->singleton(Users::class, static fn (ProjectManager $instance) => $instance->project()->auth());
		$this->app->alias(Users::class, 'appwrite.auth');
		
		$this->app->singleton(Database::class, static fn (ProjectManager $instance) => $instance->database());
		$this->app->alias(Database::class, 'appwrite.database');

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
