<?php

declare(strict_types = 1);

use Tracy\Dumper;


function dd(mixed $var): void {
	array_map(static function ($var): void {
		Dumper::dump($var);

	}, func_get_args());

	exit(1);
}
