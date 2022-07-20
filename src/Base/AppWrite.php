<?php namespace Lighth7015\AppWrite\Helpers;

use Lighth7015\AppWrite\Traits\Config;

final class AppWrite {
	
	use Config {
		Config::config as config;
		Config::project as getProject;
	}
}