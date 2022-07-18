<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Appwrite\Exception\AppwriteException;
use Appwrite\Client;
use Appwrite\Http\HttpClientOptions;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Str;

class ProjectManager {
	protected Container $app;
	protected array $projects;
	
	public function __construct(Container $app, array $projects = array()) {
		$this->app = $app;
		$this->projects = $projects;
	}

	public function project(string $name = null): AppWriteProject {
		if (!isset($this->projects[$name = $name ?? $this->getDefaultProject()])) {
			$this->projects[$name] = $this->configure($name);
		}

		return $this->projects[$name];
	}

	protected function configuration(string $name): array {
		if (!($config = $this->app->config->get(Str::finish('appwrite.projects.', $name)))) {
			throw new InvalidArgumentException(Str::finish("AppWrite project ", Str::finish($name, " is not configured.")));
		}
		else {
			return $config;
		}
	}

	protected function resolveCredentials(string $param): string {
		$success = (!Str::startsWith($param, '{')  && !Str::startsWith($param, '/') && !Str::contains($param, ':\\'));
		return call_user_func(fn (string $param): string => $success? $this->app->basePath($param): $param, $credentials);
	}

	protected function configure(string $name): AppWriteProject {
		$factory = new Factory;
		$options = HttpClientOptions::default();
		
		$config = $this->configuration($name);

		$enableAutoDiscovery = $config['credentials']['auto_discovery'] ?? ($this->getDefaultProject() === $name);
		if ($tenantId = $config['auth']['tenant_id'] ?? null) $factory = $factory->withTenantId($tenantId);

		if ($credentials = $config['credentials']['file'] ?? null) {
			$factory = $factory->withServiceAccount($this->resolveCredentials((string) $credentials));
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

		if ($proxy = $config['http_client_options']['proxy'] ?? null) $options = $options->withProxy($proxy);
		if ($timeout = $config['http_client_options']['timeout'] ?? null) $options = $options->withTimeOut((float) $timeout);

		return new AppWriteProject($factory->withHttpClientOptions($options), $config);
	}

	public function getDefaultProject(): string {
		return $this->app->config->get('appwrite.default');
	}

	public function setDefaultProject(string $name): void {
		$this->app->config->set('appwrite.default', $name);
	}

	// Pass call to default project
	public function __call($method, $parameters) {
		return $this->project()->{$method}(...$parameters);
	}
}
