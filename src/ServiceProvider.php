<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

use Laravel\Lumen\Application as Lumen;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider {
	private const key = "appwrite";

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
		return realpath(Str::finish(__DIR__, Str::finish('../', $file)));
	}

	protected function configKey(string ...$keys): string {
		return Str::finish( ServiceProvider::key, implode(".", $keys ));
	}

	protected function config(string $name = null): array {
		$default = config($this->configKey('project'), null);
		
		if (!($config = config(Str::finish('appwrite.projects.', $name), config($this->configKey('project'), null)))) {
			throw new InvalidArgumentException(Str::finish("AppWrite project ", Str::finish($name, " is not configured.")));
		}
		else {
			return $config;
		}
	}

	public function register(): void {
		// @codeCoverageIgnoreStart
		if ($this->app instanceof Lumen) {
			$this->app->configure('appwrite');
		}

		// @codeCoverageIgnoreEnd
		$this->mergeConfigFrom($this->filename('config/appwrite.php'), 'appwrite');

		$this->app->singleton(Client::class, function (Container $container) {
			$client = (new Client($app))
				->setEndpoint()
				->setProject()
				->setKey();

			// if (Arr::get())
		});
		
		
		$this->app->alias(AppWriteProjectManager::class, 'appwrite.manager');
		
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
