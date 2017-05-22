<?php

declare(strict_types = 1);

use App\Bootstrap;
use Nette\Utils\FileSystem;
use Doctrine\DBAL\Connection;

require __DIR__ . '/../vendor/autoload.php';

echo 'importing fixtures... ',

	Bootstrap::boot()
		->createContainer()
		->getByType(Connection::class)
		->executeQuery(FileSystem::read(__DIR__ . '/fixtures.sql'))
		->rowCount(),

	' rows affected', "\n"
;
