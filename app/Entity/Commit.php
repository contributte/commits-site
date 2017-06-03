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
	 * @var CommitFile[]|Collection
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
		int $total,
		int $sort = 0
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

		$this->sort = $sort;

		$this->files = new ArrayCollection;
	}


	public function getRepository(): Repository
	{
		return $this->repository;
	}


	public function getSha(?int $length = null): string
	{
		if ($length !== null && $length > 0) {
			return substr($this->sha, 0, $length);
		}

		return $this->sha;
	}


	public function hasAuthor(): bool
	{
		return $this->author !== null;
	}


	public function getAuthor(): ?User
	{
		return $this->author;
	}


	public function getAuthorName(): string
	{
		return $this->authorName;
	}


	public function hasCommitter(): bool
	{
		return $this->committer !== null;
	}


	public function getCommitter(): ?User
	{
		return $this->committer;
	}


	public function getCommitterName(): string
	{
		return $this->committerName;
	}


	public function getCommittedAt(): \DateTime
	{
		return $this->committedAt;
	}


	public function hasDifferentAuthorAndCommitter(): bool
	{
		return $this->author !== $this->committer;
	}


	public function getURL(): string
	{
		return $this->url;
	}


	public function getMessage(): string
	{
		return $this->message;
	}


	public function getFirstMessageLine(): string
	{
		$firstEOL = strpos($this->message, "\n");
		return $firstEOL === false ? $this->message : substr($this->message, 0, $firstEOL);
	}


	public function hasMultilineMessage(): bool
	{
		return strpos($this->message, "\n") !== false;
	}


	public function getOtherMessageLines(): string
	{
		$firstEOL = strpos($this->message, "\n");
		return $firstEOL === false ? '' : substr($this->message, $firstEOL);
	}


	public function getAdditions(): int
	{
		return $this->additions;
	}


	public function getDeletions(): int
	{
		return $this->deletions;
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


	/** @return CommitFile[] */
	public function getFiles(): array
	{
		return $this->files->toArray();
	}

}
