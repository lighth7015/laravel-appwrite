<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Illuminate\Support\Arr,
	Illuminate\Support\Str,
	Lighth7015\AppWrite\Traits;

use Illuminate\Contracts\Container\Container,
	Illuminate\Contracts\Foundation\Application;

class ProjectManager {
	use Traits\Config;
	
	protected Container $app;
	protected array $projects = array();
	
	public function __construct(Container $app) {
		$this->app = $app;
	}

	public function project(string $name = null): Project {
		if (is_null( $name )) {
			dd ("TODO.", $this->config());
		}
		else if (Arr::has($this->projects, $name)) {
			dd ("TODO: Implement named project");
		}
	}

	protected function configure(string $name): Project {
		dd('TODO');
	}

	// Pass call to default project
	public function __call( string $method, array $parameters) {
		dd('TODO');
		// return call_user_func_array( array($this->project(), $method), $parameters );
	}
}
