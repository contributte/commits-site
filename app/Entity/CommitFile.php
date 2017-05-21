<?php

declare(strict_types = 1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;


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


	const STATUS_ADDED = 'added';
	const STATUS_MODIFIED = 'modified';
	const STATUS_RENAMED = 'renamed';
	const STATUS_REMOVED = 'removed';


	public function __construct(
		string $filename,
		string $status,
		int $additions,
		int $deletions,
		int $changes
	) {
		$this->id = Uuid::uuid4()->toString();
		$this->filename = $filename;
		$this->status = $status;
		$this->additions = $additions;
		$this->deletions = $deletions;
		$this->changes = $changes;
	}

}
