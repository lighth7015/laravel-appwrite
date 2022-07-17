<?php declare(strict_types=1);
namespace Lighth7015\AppWrite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static Lighth7015\AppWrite\FirebaseProject project(string $name = null)
 * @method static string getDefaultProject()
 * @method static void setDefaultProject(string $name)
 * @method static Lighth7015\AppWrite\Contract\Auth auth()
 * @method static Lighth7015\AppWrite\Contract\Database database()
 * @method static Lighth7015\AppWrite\Contract\DynamicLinks dynamicLinks()
 * @method static Lighth7015\AppWrite\Contract\Firestore firestore()
 * @method static Lighth7015\AppWrite\Contract\Messaging messaging()
 * @method static Lighth7015\AppWrite\Contract\RemoteConfig remoteConfig()
 * @method static Lighth7015\AppWrite\Contract\Storage storage()
 *
 * @see Lighth7015\AppWrite\FirebaseProjectManager
 * @see Lighth7015\AppWrite\FirebaseProject
 */
final class Firebase extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'appwrite.manager';
    }
}
