<?php

declare(strict_types = 1);

namespace App\Service\Synchronizer;

use Milo\Github\Api;
use App\Entity\Commit;
use App\Entity\CommitFile;
use App\Entity\Repository;
use App\Facade\Commit\CommitPersister;
use App\Facade\Commit\CommitsOrderUpdater;
use App\Facade\Commit\UnreachableCommitsDeleter;
use App\QueryFunction\Commit\CommitsRepositoryShaMapQuery;
use App\QueryFunction\Repository\RepositoriesSortedByProjectAndNameQuery;


final class CommitSynchronizer
{

	/** @var RepositoriesSortedByProjectAndNameQuery */
	private $repositoriesQuery;

	/** @var CommitsRepositoryShaMapQuery */
	private $shaMapQuery;

	/** @var CommitsOrderUpdater */
	private $commitsOrderUpdater;

	/** @var UnreachableCommitsDeleter */
	private $unreachableCommitsDeleter;

	/** @var Api */
	private $github;

	/** @var UserSynchronizer */
	private $userSynchronizer;

	/** @var CommitPersister */
	private $commitPersister;

	/**
	 * <repository_name> => [<commit_1_sha> => <commit_1_sort>, <commit_2_sha> => <commit_2_sort>, ...]
	 * @var array<string, array<string, int>>
	 */
	private $shaMap;


	public function __construct(
		RepositoriesSortedByProjectAndNameQuery $repositoriesQuery,
		CommitsRepositoryShaMapQuery $shaMapQuery,
		CommitsOrderUpdater $commitsOrderUpdater,
		UnreachableCommitsDeleter $unreachableCommitsDeleter,
		Api $github,
		UserSynchronizer $userSynchronizer,
		CommitPersister $commitPersister

	) {
		$this->github = $github;
		$this->shaMapQuery = $shaMapQuery;
		$this->commitPersister = $commitPersister;
		$this->userSynchronizer = $userSynchronizer;
		$this->repositoriesQuery = $repositoriesQuery;
		$this->commitsOrderUpdater = $commitsOrderUpdater;
		$this->unreachableCommitsDeleter = $unreachableCommitsDeleter;
	}


	public function synchronize(): void
	{
		$repositories = $this->repositoriesQuery->get();

		foreach ($repositories as $index => $repository) {
			$this->synchronizeRepository($repository);
		}
	}


	private function synchronizeRepository(Repository $repository): void
	{
		$paginator = $this->github->paginator(sprintf('/repos/%s/commits', $repository->getName()), [
			'per_page' => 21,
		]);

		$index = 0;
		$newSHAs = [];

		foreach ($paginator as $response) {
			$commits = $this->github->decode($response);

			foreach ($commits as $commit) {
				if ($this->existsCommit($repository, $commit->sha)) {
					$this->unreachableCommitsDeleter->delete($repository, $this->shaMap[$repository->getName()], $commit->sha);
					$this->commitsOrderUpdater->update($repository, $this->shaMap[$repository->getName()], $newSHAs);
					break 2;

				} else {
					$this->synchronizeCommit($repository, $commit->sha, $index);
					$newSHAs[] = $commit->sha;
				}

				$index++;
			}
		}
	}


	private function existsCommit(Repository $repository, string $sha): bool
	{
		if ($this->shaMap === null) {
			$this->shaMap = $this->shaMapQuery->get();
		}

		return isset($this->shaMap[$repository->getName()][$sha]);
	}


	private function synchronizeCommit(
		Repository $repository,
		string $sha,
		int $index

	): void
	{
		$response = $this->github->get(sprintf('/repos/%s/commits/:sha', $repository->getName()), [
			'sha' => $sha,
		]);

		$remoteCommit = $this->github->decode($response);

		$author = $committer = null;
		if (isset($remoteCommit->author)) {
			$author = $this->userSynchronizer->synchronize(
				$remoteCommit->author->id,
				$remoteCommit->author->login,
				$remoteCommit->author->avatar_url
			);
		}

		if (isset($remoteCommit->committer)) {
			$committer = $this->userSynchronizer->synchronize(
				$remoteCommit->committer->id,
				$remoteCommit->committer->login,
				$remoteCommit->committer->avatar_url
			);
		}

		$authoredAt = new \DateTime($remoteCommit->commit->author->date);
		$authoredAt->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		$committedAt = new \DateTime($remoteCommit->commit->committer->date);
		$committedAt->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		$localCommit = new Commit(
			$repository,
			$sha,

			$author,
			$remoteCommit->commit->author->name,
			$authoredAt,

			$committer,
			$remoteCommit->commit->committer->name,
			$committedAt,

			$remoteCommit->html_url,
			$remoteCommit->commit->message,

			$remoteCommit->stats->additions,
			$remoteCommit->stats->deletions,
			$remoteCommit->stats->total,

			$index
		);

		foreach ($remoteCommit->files as $remoteFile) {
			new CommitFile(
				$localCommit,
				$remoteFile->filename,

				$remoteFile->status,
				$remoteFile->additions,
				$remoteFile->deletions,
				$remoteFile->changes
			);
		}

		$this->commitPersister->persist($localCommit);
	}

}
