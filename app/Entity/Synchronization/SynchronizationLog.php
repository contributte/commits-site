<?php

declare(strict_types = 1);

namespace App\Entity\Synchronization;

use App\Entity\ID;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(indexes = {
 *     @ORM\Index(columns = {"started_at"}),
 *     @ORM\Index(columns = {"finished_at"})
 * })
 */
class SynchronizationLog
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $id;

	/**
	 * @ORM\Column(type = "datetime_immutable")
	 * @var \DateTimeImmutable
	 */
	private $startedAt;

	/**
	 * @ORM\Column(type = "datetime_immutable", nullable = true)
	 * @var \DateTimeImmutable|null
	 */
	private $finishedAt;

	/**
	 * @ORM\Column(type = "integer", nullable = true)
	 * @var int|null
	 */
	private $memoryPeak;

	/**
	 * @ORM\OneToMany(targetEntity = "RepositoryLog", mappedBy = "synchronizationLog", cascade = {"persist"})
	 * @ORM\JoinColumn(onDelete = "CASCADE")
	 * @var RepositoryLog[]|Collection<int, RepositoryLog>
	 */
	private $repositoryLogs;


	public function __construct()
	{
		$this->id = ID::generate();
		$this->startedAt = new \DateTimeImmutable;
		$this->repositoryLogs = new ArrayCollection;
	}


	public function addRepositoryLog(RepositoryLog $repositoryLog): self
	{
		if (!$this->repositoryLogs->contains($repositoryLog)) {
			$this->repositoryLogs->add($repositoryLog);
		}

		return $this;
	}


	public function getStartedAt(): \DateTimeImmutable
	{
		return $this->startedAt;
	}


	public function finish(): void
	{
		$this->memoryPeak = memory_get_peak_usage(true);
		$this->finishedAt = new \DateTimeImmutable;
	}


	public function getApiCalls(): int
	{
		return (int) array_sum(array_map(static function (RepositoryLog $repositoryLog): int {
			return $repositoryLog->getApiCalls();

		}, $this->repositoryLogs->toArray()));
	}


	public function getNewCommits(): int
	{
		return (int) array_sum(array_map(static function (RepositoryLog $repositoryLog): int {
			return $repositoryLog->getNewCommits();

		}, $this->repositoryLogs->toArray()));
	}


	public function getDeletedCommits(): int
	{
		return (int) array_sum(array_map(static function (RepositoryLog $repositoryLog): int {
			return $repositoryLog->getDeletedCommits();

		}, $this->repositoryLogs->toArray()));
	}


	public function getMemoryPeak(): ?int
	{
		return $this->memoryPeak;
	}


	public function getElapsedSeconds(): int
	{
		return ($this->finishedAt === null ? time() : $this->finishedAt->getTimestamp())
			- $this->startedAt->getTimestamp();
	}

}
