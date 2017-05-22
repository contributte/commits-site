<?php

declare(strict_types = 1);

namespace App\Facade\Commit;

use App\Entity\Repository;
use Doctrine\DBAL\Connection;


final class UnreachableCommitsDeleter
{

	/** @var Connection */
	private $connection;


	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}


	public function delete(Repository $repository, array & $allSHAs, string $lastSHA): void
	{
		$toDelete = [];

		foreach ($allSHAs as $sha => $sort) {
			if ($sha === $lastSHA) {
				break;

			} else {
				$toDelete[] = $sha;
				unset($allSHAs[$sha]);
			}
		}

		if (count($toDelete)) {
			$this->connection->executeUpdate('
				DELETE FROM `commit`
				WHERE repository = :repository
					AND sha IN (:shas)

			', [
				'repository' => $repository->getName(),
				'shas' => $toDelete,

			], [
				'shas' => Connection::PARAM_STR_ARRAY,
			]);
		}
	}

}
