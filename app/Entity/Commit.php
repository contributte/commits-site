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
	 * @ORM\JoinColumn(name = "repository", referencedColumnName = "name", onDelete = "CASCADE")
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
	 * @ORM\Column(type = "datetime")
	 * @var \DateTime
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
	 * @ORM\Column(type = "datetime")
	 * @var \DateTime
	 */
	private $committedAt;

	/**
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $url;

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
		\DateTime $authoredAt,
		?User $committer,
		string $committerName,
		\DateTime $committedAt,
		string $url,
		string $message,
		int $additions,
		int $deletions,
		int $total
	) {
		$this->sha = $sha;
		$this->repository = $repository;
		$repository->addCommit($this);

		$this->author = $author;
		$this->authorName = $authorName;
		$this->authoredAt = $authoredAt;

		$this->committer = $committer;
		$this->committerName = $committerName;
		$this->committedAt = $committedAt;

		$this->url = $url;
		$this->message = $message;

		$this->additions = $additions;
		$this->deletions = $deletions;
		$this->total = $total;

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
