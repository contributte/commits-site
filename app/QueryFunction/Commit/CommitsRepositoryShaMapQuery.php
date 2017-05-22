<?php

declare(strict_types = 1);

namespace App\QueryFunction\Commit;

use Doctrine\DBAL\Connection;


final class CommitsRepositoryShaMapQuery
{

	/** @var Connection */
	private $connection;


	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}


	/** @return array<string, array<string, int>> */
	public function get(): array
	{
		$rows = $this->connection->fetchAll('
				SELECT r.name, c.sha, c.sort
				FROM `commit` c
				JOIN repository r ON c.repository = r.name
				ORDER BY c.sort ASC
			');

		$shaMap = [];
		foreach ($rows as $row) {
			$sName = (string) $row['name'];

			if (!isset($shaMap[$sName])) {
				$shaMap[$sName] = [];
			}

			$shaMap[$sName][(string) $row['sha']] = (int) $row['sort'];
		}

		return $shaMap;
	}

}
