<?php declare(strict_types=1);
namespace Lighth7015\AppWrite;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as Provider,
    Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends Provider {
    private static string $provider = 'appwrite';

    protected $policies = [
        // Model::class => ModelPolicy::class
    ];

	public function boot(): void {
        $this->registerPolicies();

        Auth::provider(static::$provider, function($app, array $config) {

        });
	}
}
