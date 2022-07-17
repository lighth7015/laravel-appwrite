<?php declare(strict_types=1);
namespace lighth7015\AppWrite;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Appwrite\Exception\AppwriteException;
use Appwrite\Client;
use Appwrite\Http\HttpClientOptions;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Str;

class FirebaseProjectManager {
	public function __construct(protected Container $app, protected array $projects = array()) { }

	public function project(?string $name = null): FirebaseProject {
		$name = $name ?? $this->getDefaultProject();

		if (!isset($this->projects[$name])) {
			$this->projects[$name] = $this->configure($name);
		}

		return $this->projects[$name];
	}

	protected function configuration(string $name): array {
		if (!($config = $this->app->config->get(Str::finish('firebase.projects.', $name)))) {
			throw new InvalidArgumentException("Firebase project [{$name}] not configured.");
		}

		return $config;
	}

	protected function resolveCredentials(string $credentials): string {
		$isJsonString = \str_starts_with($credentials, '{');
		$isAbsoluteLinuxPath = \str_starts_with($credentials, '/');
		$isAbsoluteWindowsPath = \str_contains($credentials, ':\\');

		$isRelativePath = !$isJsonString && !$isAbsoluteLinuxPath && !$isAbsoluteWindowsPath;
		return $isRelativePath ? $this->app->basePath($credentials) : $credentials;
	}

	protected function configure(string $name): FirebaseProject {
		$factory = new Factory;
		$options = HttpClientOptions::default();
		
		$config = $this->configuration($name);

		$enableAutoDiscovery = $config['credentials']['auto_discovery'] ?? ($this->getDefaultProject() === $name);
		if ($tenantId = $config['auth']['tenant_id'] ?? null) $factory = $factory->withTenantId($tenantId);

		if ($credentials = $config['credentials']['file'] ?? null) {
			$resolvedCredentials = $this->resolveCredentials((string) $credentials);
			$factory = $factory->withServiceAccount($resolvedCredentials);
		}

		if (!$enableAutoDiscovery) $factory = $factory->withDisabledAutoDiscovery();
		if ($databaseUrl = $config['database']['url'] ?? null) $factory = $factory->withDatabaseUri($databaseUrl);

		if ($authVariableOverride = $config['database']['auth_variable_override'] ?? null)
			$factory = $factory->withDatabaseAuthVariableOverride($authVariableOverride);

		if ($defaultStorageBucket = $config['storage']['default_bucket'] ?? null)
			$factory = $factory->withDefaultStorageBucket($defaultStorageBucket);

		if ($cacheStore = $config['cache_store'] ?? null) {
			$cache = $this->app->make('cache')->store($cacheStore);

			if ($cache instanceof CacheInterface) {
				$cache = new Psr16Adapter($cache);
				$factory = $factory ->withVerifierCache($cache)
								->withAuthTokenCache($cache);
			}
			else {
				throw new InvalidArgumentException('The cache store must be an instance of a PSR-6 or PSR-16 cache');
			}
		}

		if ($logChannel = $config['logging']['http_log_channel'] ?? null) {
			$factory = $factory->withHttpLogger($this->app->make('log')->channel($logChannel));
		}

		if ($logChannel = $config['logging']['http_debug_log_channel'] ?? null) {
			$factory = $factory->withHttpDebugLogger($this->app->make('log')->channel($logChannel));
		}

		if ($proxy = $config['http_client_options']['proxy'] ?? null) $options = $options->withProxy($proxy);
		if ($timeout = $config['http_client_options']['timeout'] ?? null) $options = $options->withTimeOut((float) $timeout);

		return new FirebaseProject($factory->withHttpClientOptions($options), $config);
	}

	public function getDefaultProject(): string {
		return $this->app->config->get('firebase.default');
	}

	public function setDefaultProject(string $name): void {
		$this->app->config->set('firebase.default', $name);
	}

	// Pass call to default project
	public function __call($method, $parameters) {
		return $this->project()->{$method}(...$parameters);
	}
}
