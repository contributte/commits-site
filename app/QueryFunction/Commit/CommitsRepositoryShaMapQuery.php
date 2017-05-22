<?php

declare(strict_types = 1);

namespace App\QueryFunction\Commit;

use Doctrine\DBAL\Connection;


final class CommitsRepositoryShaMapQuery
{

	private Connection $connection;


	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}


	/** @return array<string, array<string, int>> */
	public function get(): array
	{
		/** @var array<int, array{id: string, sha: string, sort: string}> $rows */
		$rows = $this->connection->fetchAllAssociative('
				SELECT r.id, c.sha, c.sort
				FROM `commit` c
				JOIN repository r ON c.repository = r.id
				ORDER BY c.sort ASC
			');

		$shaMap = [];
		foreach ($rows as $row) {
			$sID = $row['id'];

			if (!isset($shaMap[$sID])) {
				$shaMap[$sID] = [];
			}

			$shaMap[$sID][$row['sha']] = (int) $row['sort'];
		}

		return $shaMap;
	}

}
