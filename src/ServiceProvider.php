<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Appwrite\Client,
	InvalidArgumentException;

use Illuminate\Contracts\Container\Container,
	Illuminate\Support\Arr,
	Illuminate\Support\Str;

use Laravel\Lumen\Application as Lumen;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider {
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

	private function filename(string $file): string {
		return realpath(Str::finish(__DIR__, Str::finish('/../', $file)));
	}

	protected function config(string | null ...$keys): array | string | bool | null {
		$path = implode( ".", array_reduce( $keys, function (array $keys, string | null $key): array {
			if (is_string($key)) array_push( $keys, $key );
			return $keys;
		}, array()));

		if (is_null( $config = config(str(__NAMESPACE__)->after('\\')->lower()->toString(), null)) === false) {
			$config = Arr::get( $config, Str::finish( "projects.", Arr::get( $config, "project" )));
			return strlen($path) > 0? Arr::get( $config, $path ): $config;
		}
		
		return $config;
	}

	public function register(): void {
		// @codeCoverageIgnoreStart
		if ($this->app instanceof Lumen) {
			$this->app->configure('appwrite');
		}

		// @codeCoverageIgnoreEnd
		$this->mergeConfigFrom($this->filename('config/appwrite.php'), 'appwrite');

		$this->app->singleton(Client::class, function (Container $container) {
			(($client = new Client))
				->setEndpoint($this->config('endpoint'))
				->setProject($this->config('credentials.project-id'))
				->setKey($this->config('credentials.api-key'));

			$selfSigned = Str::is($this->config('protocol'), 'https') && env('APP_DEBUG');
			return $selfSigned? $client->setSelfSigned(): $client;
		});
	
		//$this->app->alias(Client::class, 'appwrite.manager');
		
		// $this->app->singleton(Appwrite\Services\Accont::class, static fn (Container $app) => $app->make(AppWriteProjectManager::class)->project()->auth());
		// $this->app->alias(Appwrite\Services\Account::class, 'appwrite.auth');

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
