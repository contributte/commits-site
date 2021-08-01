<?php

declare(strict_types = 1);

namespace App\QueryFunction\Synchronization;


final class SynchronizationLogNotFoundException extends \Exception
{

	public static function latestFinished(): self
	{
		return new self('No finished syncrhonization log found.');
	}

}
