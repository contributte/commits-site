<?php

declare(strict_types = 1);

namespace App\Entity;


class CommitFile
{

	/** @var string */
	private $id;

	/** @var string */
	private $filename;

	/** @var string */
	private $status;

	/** @var int */
	private $additions;

	/** @var int */
	private $deletions;

	/** @var int */
	private $changes;


	private const STATUS_ADDED = 'added';
	private const STATUS_MODIFIED = 'modified';
	private const STATUS_RENAMED = 'renamed';
	private const STATUS_REMOVED = 'removed';


	public function __construct(
		string $filename,
		string $status,
		int $additions,
		int $deletions,
		int $changes

	) {
		$this->status = $status;
		$this->changes = $changes;
		$this->id = ID::generate();
		$this->filename = $filename;
		$this->additions = $additions;
		$this->deletions = $deletions;
	}

}
