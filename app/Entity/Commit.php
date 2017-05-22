<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 *
 * @ORM\Table(indexes = {
 *     @ORM\Index(columns = {"committed_at"}),
 *     @ORM\Index(columns = {"sort"})
 * })
 */
class Commit
{

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity = "Repository", inversedBy = "commits")
	 * @ORM\JoinColumn(name = "repository", referencedColumnName = "id", onDelete = "CASCADE")
	 *
	 * @var Repository
	 */
	private $repository;

	/**
	 * @ORM\Id
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $sha;

	/**
	 * @ORM\ManyToOne(targetEntity = "User")
	 * @ORM\JoinColumn(name = "author", onDelete = "SET NULL")
	 * @var User|null
	 */
	private $author;

	/**
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $authorName;

	/**
	 * @ORM\Column(type = "datetime_immutable")
	 * @var \DateTimeImmutable
	 */
	private $authoredAt;

	/**
	 * @ORM\ManyToOne(targetEntity = "User")
	 * @ORM\JoinColumn(name = "committer", onDelete = "SET NULL")
	 * @var User|null
	 */
	private $committer;

	/**
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $committerName;

	/**
	 * @ORM\Column(type = "datetime_immutable")
	 * @var \DateTimeImmutable
	 */
	private $committedAt;


	/**
	 * @ORM\Column(type = "text")
	 * @var string
	 */
	private $message;

	/**
	 * @ORM\Column(type = "integer")
	 * @var int
	 */
	private $additions;

	/**
	 * @ORM\Column(type = "integer")
	 * @var int
	 */
	private $deletions;

	/**
	 * @ORM\Column(type = "integer")
	 * @var int
	 */
	private $total;

	/**
	 * @ORM\OneToMany(targetEntity = "CommitFile", mappedBy = "commit", cascade = {"persist"})
	 * @ORM\OrderBy({"filename" = "ASC"})
	 * @var CommitFile[]|Collection<int, CommitFile>
	 */
	private $files;

	/**
	 * @ORM\Column(type = "integer")
	 * @var int
	 */
	private $sort = 0;


	public function __construct(
		Repository $repository,
		string $sha,
		?User $author,
		string $authorName,
		\DateTimeImmutable $authoredAt,
		?User $committer,
		string $committerName,
		\DateTimeImmutable $committedAt,
		string $message,
		int $additions,
		int $deletions,
		int $total

	) {
		$this->sha = $sha;
		$repository->addCommit($this);
		$this->repository = $repository;

		$this->author = $author;
		$this->authoredAt = $authoredAt;
		$this->authorName = $authorName;

		$this->committer = $committer;
		$this->committedAt = $committedAt;
		$this->committerName = $committerName;

		$this->total = $total;
		$this->author = $author;
		$this->message = $message;

		$this->total = $total;
		$this->additions = $additions;
		$this->committer = $committer;
		$this->deletions = $deletions;

		$this->files = new ArrayCollection;
	}


	public function addFile(CommitFile $file): self
	{
		if (!$this->hasFile($file)) {
			$this->files->add($file);
		}

		return $this;
	}


	public function hasFile(CommitFile $file): bool
	{
		return $this->files->contains($file);
	}

}
