<?php

declare(strict_types=1);

namespace Kreait\Laravel\Firebase\Tests;

use Kreait\Firebase;

/**
 * @internal
 */
final class ServiceProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_components(): void
    {
        $this->app->config->set('firebase.projects.app.credentials.file', \realpath(__DIR__.'/_fixtures/service_account.json'));

        $this->assertInstanceOf(Firebase\Contract\Auth::class, $this->app->make(Firebase\Contract\Auth::class));
        $this->assertInstanceOf(Firebase\Contract\Database::class, $this->app->make(Firebase\Contract\Database::class));
        $this->assertInstanceOf(Firebase\Contract\DynamicLinks::class, $this->app->make(Firebase\Contract\DynamicLinks::class));
        $this->assertInstanceOf(Firebase\Contract\Messaging::class, $this->app->make(Firebase\Contract\Messaging::class));
        $this->assertInstanceOf(Firebase\Contract\RemoteConfig::class, $this->app->make(Firebase\Contract\RemoteConfig::class));
        $this->assertInstanceOf(Firebase\Contract\Storage::class, $this->app->make(Firebase\Contract\Storage::class));
    }

    /**
     * @test
     */
    public function it_does_not_provide_optional_components(): void
    {
        $this->expectException(\Throwable::class);
        $this->expectDeprecationMessageMatches('/unable/i');

        $this->app->make(Firebase\Contract\Firestore::class);
    }
}
