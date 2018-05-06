<?php

declare(strict_types = 1);

namespace Tests;

use Tester\Helpers;
use Tester\Environment;
use Nette\Configurator;


require_once __DIR__ . '/../vendor/autoload.php';


final class Bootstrap
{

	public static function boot(): Configurator
	{
		Environment::setup();

		$configurator = \App\Bootstrap::boot();

		$tempDir = __DIR__ . '/../var/temp/tests';
		$configurator->setTempDirectory($tempDir);

		Helpers::purge($tempDir);

		return $configurator;
	}

}
