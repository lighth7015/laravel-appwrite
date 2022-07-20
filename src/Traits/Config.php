<?php namespace Lighth7015\AppWrite\Traits;
use Illuminate\Support\Str,
	Illuminate\Support\Arr;

use Lighth7015\AppWrite\Helpers\AppWrite;

trait Config {
	protected function filename(string $file): string {
		return AppWrite::filename($file);
	}

	protected function getProjectName(): string {
		return AppWrite::project();
	}

	protected function config(string | null ...$keys) {
		return forward_static_call_array( array( AppWrite::class, 'config' ), $keys );
	}

	protected function resolveCredentials(string $param): string {
		$success = (!Str::startsWith($param, '{')  && !Str::startsWith($param, '/') && !Str::contains($param, ':\\'));
		return call_user_func(fn (string $param): string => $success? $this->app->basePath($param): $param, $credentials);
	}
}