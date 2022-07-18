<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Lighth7015\AppWrite\Traits;

use Illuminate\Contracts\Container\Container,
	Illuminate\Contracts\Foundation\Application;

use Appwrite\Exception\AppwriteException;
use Appwrite\Client;

use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Illuminate\Support\Str;

class ProjectManager {
	use Traits\Config;
	
	protected Container $app;
	protected array $projects = array();
	
	public function __construct(Container $app) {
		$this->app = $app;
	}

	public function project(string $name = null): AppWriteProject {
		if (!isset($this->projects[$name = $name ?? $this->getDefaultProject()])) {
			$this->projects[$name] = $this->configure($name);
		}

		return $this->projects[$name];
	}

	protected function configure(string $name): AppWriteProject {
		dd('TODO');
	}

	// Pass call to default project
	public function __call( string $method, array $parameters) {
		return call_user_func_array( array($this->project(), $method), $parameters );
	}
}
