<?php

declare(strict_types = 1);

namespace App\Facade\Commit;

use App\Entity\Repository;
use Doctrine\DBAL\Connection;


final class CommitsOrderUpdater
{

	/** @var Connection */
	private $connection;


	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}


	/**
	 * Sometimes commits have same author date (due to rebase)
	 * so we need to keep the same order as received from GitHub API
	 *
	 * @param  Repository $repository
	 * @param  string[] $currentSHAs
	 * @param  string[] $newSHAs
	 */
	public function update(Repository $repository, array $currentSHAs, array $newSHAs): void
	{
		if (count($newSHAs) === 0) {
			return ;
		}

		$sortSQL = '
			UPDATE `commit` SET sort = (CASE sha %s END)
			WHERE repository = :repository
		';

		$sortCases = '';
		$sortSqlParams = [];

		// new commits first
		foreach ($newSHAs as $key => $sha) {
			$sortCases .= sprintf(' WHEN :sha_%d THEN :sort_%d', $key, $key);
			$sortSqlParams['sha_' . $key] = $sha;
			$sortSqlParams['sort_' . $key] = $key;
		}

		$offset = count($newSHAs);

		// old commits afterwards
		foreach ($currentSHAs as $sha => $sort) {
			$newSort = $offset + (int) $sort;
			$sortCases .= sprintf(' WHEN :sha_%d THEN :sort_%d ', $newSort, $newSort);
			$sortSqlParams['sha_' . $newSort] = $sha;
			$sortSqlParams['sort_' . $newSort] = $newSort;
		}

		$sortSqlParams['repository'] = $repository->getName();

		$this->connection->executeQuery(sprintf($sortSQL, $sortCases), $sortSqlParams, [
			'shas' => Connection::PARAM_STR_ARRAY,
		]);
	}

}
