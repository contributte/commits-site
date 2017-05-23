<?php

declare(strict_types = 1);

namespace App\Service\Synchronizer;

use Milo\Github\Api;
use App\Entity\Commit;
use App\Entity\CommitFile;
use App\Entity\Repository;
use App\Facade\Commit\CommitPersister;
use App\Facade\Commit\CommitsOrderUpdater;
use App\Entity\Synchronization\RepositoryLog;
use App\Facade\Commit\UnreachableCommitsDeleter;
use App\Entity\Synchronization\SynchronizationLog;
use App\QueryFunction\Commit\CommitsRepositoryShaMapQuery;
use App\Facade\SynchronizationLog\SynchronizationLogPersister;
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

	/** @var SynchronizationLogPersister */
	private $synchronizationLogPersister;

	/**
	 * <repository_name> => [<commit_1_sha> => <commit_1_sort>, <commit_2_sha> => <commit_2_sort>, ...]
	 * @var array
	 */
	private $shaMap;


	public function __construct(
		RepositoriesSortedByProjectAndNameQuery $repositoriesQuery,
		CommitsRepositoryShaMapQuery $shaMapQuery,
		CommitsOrderUpdater $commitsOrderUpdater,
		UnreachableCommitsDeleter $unreachableCommitsDeleter,
		Api $github,
		UserSynchronizer $userSynchronizer,
		CommitPersister $commitPersister,
		SynchronizationLogPersister $synchronizationLogPersister

	) {
		$this->github = $github;
		$this->shaMapQuery = $shaMapQuery;
		$this->commitPersister = $commitPersister;
		$this->userSynchronizer = $userSynchronizer;
		$this->repositoriesQuery = $repositoriesQuery;
		$this->commitsOrderUpdater = $commitsOrderUpdater;
		$this->unreachableCommitsDeleter = $unreachableCommitsDeleter;
		$this->synchronizationLogPersister = $synchronizationLogPersister;
	}


	public function synchronize(
		callable $onSynchronizationStart = null,
		callable $onRepositoryStart = null,
		callable $onCommitStart = null,
		callable $onCommitExists = null,
		callable $onUnreachablesDeleted = null,
		callable $onCommitOrderUpdated = null,
		callable $onSynchronizationFinish = null

	): void
	{
		$syncLog = new SynchronizationLog;
		$repositories = $this->repositoriesQuery->get();
		$onSynchronizationStart && $onSynchronizationStart(count($repositories));

		foreach ($repositories as $index => $repository) {
			$onRepositoryStart && $onRepositoryStart($index, $repository);

			$this->synchronizeRepository(
				$syncLog,
				$repository,
				$onCommitStart,
				$onCommitExists,
				$onUnreachablesDeleted,
				$onCommitOrderUpdated
			);
		}

		$syncLog->finish();

		$this->synchronizationLogPersister->persist($syncLog);

		$onSynchronizationFinish && $onSynchronizationFinish($syncLog);
	}


	private function synchronizeRepository(
		SynchronizationLog $syncLog,
		Repository $repository,
		callable $onCommitStart = null,
		callable $onCommitExists = null,
		callable $onUnreachablesDeleted = null,
		callable $onCommitOrderUpdated = null

	): void
	{
		$repositoryLog = new RepositoryLog($syncLog, $repository);

		$paginator = $this->github->paginator(sprintf('/repos/%s/commits', $repository->getName()), [
			'per_page' => 21,
		]);

		$index = 0;
		$newSHAs = [];

		foreach ($paginator as $response) {
			$repositoryLog->apiCall();
			$commits = $this->github->decode($response);

			foreach ($commits as $commit) {
				$onCommitStart && $onCommitStart($index, $commit->sha);

				if ($this->existsCommit($repository, $commit->sha)) {
					$onCommitExists && $onCommitExists();

					$deleted = $this->unreachableCommitsDeleter->delete($repository, $this->shaMap[$repository->getName()], $commit->sha);
					$repositoryLog->deletedCommits($deleted);
					$onUnreachablesDeleted && $onUnreachablesDeleted($deleted);

					$this->commitsOrderUpdater->update($repository, $this->shaMap[$repository->getName()], $newSHAs);
					$onCommitOrderUpdated && $onCommitOrderUpdated();
					break 2;

				} else {
					$this->synchronizeCommit($repositoryLog, $repository, $commit->sha, $index);
					$newSHAs[] = $commit->sha;
				}

				$index++;
			}
		}

		$repositoryLog->finish();
	}


	private function existsCommit(Repository $repository, string $sha): bool
	{
		if (!$this->shaMap) {
			$this->shaMap = $this->shaMapQuery->get();
		}

		return isset($this->shaMap[$repository->getName()][$sha]);
	}


	private function synchronizeCommit(
		RepositoryLog $repositoryLog,
		Repository $repository,
		string $sha,
		int $index

	): void
	{
		$response = $this->github->get(sprintf('/repos/%s/commits/:sha', $repository->getName()), [
			'sha' => $sha,
		]);

		$repositoryLog->apiCall();
		$remoteCommit = $this->github->decode($response);

		$author = $committer = null;
		if (isset($remoteCommit->author)) {
			$author = $this->userSynchronizer->synchronize(
				$repositoryLog,
				$remoteCommit->author->id,
				$remoteCommit->author->login,
				$remoteCommit->author->avatar_url
			);
		}

		if (isset($remoteCommit->committer)) {
			$committer = $this->userSynchronizer->synchronize(
				$repositoryLog,
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

		$repositoryLog->newCommit();
	}

}
