<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Illuminate\Support\Arr,
	Illuminate\Contracts\Container\Container,
	Lighth7015\AppWrite\Traits;

class ProjectManager {
	use Traits\Config;
	
	protected Container $app;
	protected array $projects = array();
	
	public function __construct(Container $app) {
		$this->app = $app;
	}

	public function project(string $name = null): Project {
		if (is_string($name) && ($project = Arr::get($this->projects, $name, false))) {
			return $project;
		}
		else {
			Arr::set( $this->projects, $this->getProjectName(), ( $project = 
				resolve(Project::class, array( 'project' => $this->projectName() ))
			));
			
			return $project;
		}
	}

	// Pass call to default project
	public function __call( string $method, array $parameters) {
		return call_user_func_array( array($this->project(), $method), $parameters );
	}
}