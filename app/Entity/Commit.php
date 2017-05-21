<?php

declare(strict_types = 1);

namespace App\Entity;


class Commit
{

	/** @var Repository */
	private $repository;

	/** @var string */
	private $sha;

	/** @var User|null */
	private $author;

	/** @var string */
	private $authorName;

	/** @var \DateTimeImmutable */
	private $authoredAt;

	/** @var User|null */
	private $committer;

	/** @var string */
	private $committerName;

	/** @var \DateTimeImmutable */
	private $committedAt;

	/** @var string */
	private $message;

	/** @var int */
	private $additions;

	/** @var int */
	private $deletions;

	/** @var int */
	private $total;


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
		$this->total = $total;
		$this->author = $author;
		$this->message = $message;
		$this->additions = $additions;
		$this->committer = $committer;
		$this->deletions = $deletions;
		$this->repository = $repository;
		$this->authorName = $authorName;
		$this->authoredAt = $authoredAt;
		$this->committedAt = $committedAt;
		$this->committerName = $committerName;
	}

}
