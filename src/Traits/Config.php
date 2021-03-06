<?php namespace Lighth7015\AppWrite\Traits;
use Illuminate\Support\Str,
	Illuminate\Support\Arr;

trait Config {
		protected function filename(string $file): string {
		return realpath(Str::finish(__DIR__, Str::finish('/../../', $file)));
	}

	protected function project(): string {
		$namespaces = array_map( fn(string $it) => strtolower($it), explode( '\\', __NAMESPACE__ ));
		$getParentNamespace = fn(int $index) => Arr::get( $namespaces, $index );
		
		return Arr::get( config( call_user_func( $getParentNamespace, 1 ), null), "project" );
	}

	protected function config(string | null ...$keys) {
		$namespaces = array_map( fn(string $it) => strtolower($it), explode( '\\', __NAMESPACE__ ));
		$getParentNamespace = fn(int $index) => Arr::get( $namespaces, $index );
		
		$path = implode( ".", array_reduce( $keys, function (array $keys, string | null $key) {
			if (is_string($key)) array_push( $keys, $key );
			return $keys;
		}, array()));

		if (is_null( $config = config( call_user_func( $getParentNamespace, 1 ), null)) === false) {
			$config = Arr::get( $config, Str::finish( "projects.", $this->getProjectName()));
			return strlen($path) > 0? Arr::get( $config, $path ): $config;
		}
		
		return $config;
	}

	protected function resolveCredentials(string $param): string {
		$success = (!Str::startsWith($param, '{')  && !Str::startsWith($param, '/') && !Str::contains($param, ':\\'));
		return call_user_func(fn (string $param): string => $success? $this->app->basePath($param): $param, $credentials);
	}
}