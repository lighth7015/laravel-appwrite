<?php declare(strict_types=1);
namespace lighth7015\AppWrite;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

use Laravel\Lumen\Application as Lumen;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider {

	public function boot(): void {
		// @codeCoverageIgnoreStart
		if (!$this->app->runningInConsole() || !! $this->app instanceof Lumen) {
			return;
		}

		// @codeCoverageIgnoreEnd
		$filename = Str::finish(__DIR__, '/../config/appwrite.php');
		$this->publishes(array(
			$filename => $this->app->configPath('appwrite.php')
		), 'config');
	}

	public function register(): void
	{
		// @codeCoverageIgnoreStart
		if ($this->app instanceof Lumen) {
			$this->app->configure('appwrite');
		}

		// @codeCoverageIgnoreEnd
		$this->mergeConfigFrom(__DIR__.'/../config/appwrite.php', 'appwrite');

		// $this->app->singleton(FirebaseProjectManager::class, static fn (Container $app) => new FirebaseProjectManager($app));
		// $this->app->alias(FirebaseProjectManager::class, 'appwrite.manager');
		
		$this->app->singleton(Appwrite\Services\Accont::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->auth());
		$this->app->alias(Appwrite\Services\Account::class, 'appwrite.auth');

		// $this->app->singleton(Firebase\Contract\Database::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->database());
		// $this->app->alias(Firebase\Contract\Database::class, 'appwrite.database');

		// $this->app->singleton(Firebase\Contract\DynamicLinks::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->dynamicLinks());
		// $this->app->alias(Firebase\Contract\DynamicLinks::class, 'appwrite.dynamic_links');

		// $this->app->singleton(Firebase\Contract\Firestore::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->firestore());
		// $this->app->alias(Firebase\Contract\Firestore::class, 'appwrite.firestore');

		// $this->app->singleton(Firebase\Contract\Messaging::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->messaging());
		// $this->app->alias(Firebase\Contract\Messaging::class, 'appwrite.messaging');

		// $this->app->singleton(Firebase\Contract\RemoteConfig::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->remoteConfig());
		// $this->app->alias(Firebase\Contract\RemoteConfig::class, 'appwrite.remote_config');

		// $this->app->singleton(Firebase\Contract\Storage::class, static fn (Container $app) => $app->make(FirebaseProjectManager::class)->project()->storage());
		// $this->app->alias(Firebase\Contract\Storage::class, 'appwrite.storage');
	}
}
