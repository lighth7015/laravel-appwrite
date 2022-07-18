<?php
namespace Lighth7015\AppWrite\Traits;
use Illuminate\Support\Str,
    Illuminate\Support\Arr;

trait Config {
	private function filename(string $file): string {
		return realpath(Str::finish(__DIR__, Str::finish('/../../', $file)));
	}

	protected function config(string | null ...$keys) {
        $namespaces = explode( '\\', __NAMESPACE__ );
        $getParentNamespace = fn(int $index) => Arr::get( $namespaces, $index );
        
        dd(call_user_func( $getParentNamespace, 1 ));
        
        $path = implode( ".", array_reduce( $keys, function (array $keys, string | null $key) {
			if (is_string($key)) array_push( $keys, $key );
			return $keys;
		}, array()));

		if (is_null( $config = config( $namespace, null)) === false) {
			$config = Arr::get( $config, Str::finish( "projects.", Arr::get( $config, "project" )));
			return strlen($path) > 0? Arr::get( $config, $path ): $config;
		}
		
		return $config;
	}

    protected function resolveCredentials(string $param): string {
		$success = (!Str::startsWith($param, '{')  && !Str::startsWith($param, '/') && !Str::contains($param, ':\\'));
		return call_user_func(fn (string $param): string => $success? $this->app->basePath($param): $param, $credentials);
	}
}