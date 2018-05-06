<?php

declare(strict_types = 1);

namespace Tests;

use Tester\Environment;
use Nette\Configurator;

require_once __DIR__ . '/../vendor/autoload.php';


final class Bootstrap
{

	public static function boot(): Configurator
	{
		Environment::setup();
		return \App\Bootstrap::boot();
	}

}
