<?php namespace Lighth7015\AppWrite\Auth;

use Illuminate\Auth\GenericUser,
	Illuminate\Contracts\Auth\Authenticatable,
	Illuminate\Contracts\Auth\UserProvider,
	Illuminate\Support\Arr;

class AppWriteUserProvider implements UserProvider {
	/**
	 * This method is called from subsequent calls until the session expires. As
	 * you don't have a local users database, let's assume your identifier saved
	 * into the session is fine. 
	 * 
	 * Session cookies are encrypted by default. This avoid calling the external 
	 * service on every navigation.  The downside to this approach,  is that you 
	 * won't know when the user's authorization expires in your external service
	 * until the local session expires.
	 * 
	 * Ideally, you'll want to use a lower session duration so that the issue is 
	 * handled as quickly as possible.
	 * 
	 * alternatively, you can save it encrypted the user's credentials, and call
	 * the external service every time;  this comes at the expense of performing 
	 * an API request potentially every page load, which makes your app an order
	 * of magintude slower, but is the (most) secure way.
	 * 
	 */
	public function retrieveById($identifier) {
		return new GenericUser([
			'id' => $identifier,
			'email' => $identifier,
		]);
	}

	public function retrieveByToken($identifier, $token) {
		return null;
	}

	public function updateRememberToken(Authenticatable $user, $token) {
	}

	public function retrieveByCredentials(array $credentials) {
		$response = null;
		
		// GenericUser is a class from Laravel Auth System
		if (array_key_exists('email', $credentials)) {
			$email = Arr::get( $credentials, 'email' );
			$response = new GenericUser(array( 'id' => $email, 'email' => $email ));
		}
		
		return $response;
	}

	public function validateCredentials(Authenticatable $user, array $credentials) {
		if (array_key_exists('password', $credentials)) {
			// This is a simplified usage of Laravel's HTTP Client to call the external API
			// You might need to send more info to the external service.
			// Please refer to the HTTP Client docs to learn how to use it properly.
			$response = Http::post('https://example.com/authenticate', [
				// $user is the GenericUser instance created in
				// the retrieveByCredentials() method above.
				'email' => $user->email,
				'password' => $credentials['password'],
			]);
	
			return $response->ok();
		}
		else {
			return false;
		}

	}
}