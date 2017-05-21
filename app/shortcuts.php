<?php

declare(strict_types = 1);

function dd($var): void {
	array_map('Tracy\Dumper::dump', func_get_args());
	exit(1);
}
