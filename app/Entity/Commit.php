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

	/** @var \DateTime */
	private $authoredAt;

	/** @var User|null */
	private $committer;

	/** @var string */
	private $committerName;

	/** @var \DateTime */
	private $committedAt;

	/** @var string */
	private $url;

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
		$this->repository = $repository;
		$this->sha = $sha;
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
	}

}
