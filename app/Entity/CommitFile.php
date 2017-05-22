<?php

declare(strict_types = 1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;


/** @ORM\Entity */
class CommitFile
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity = "Commit", inversedBy = "files")
	 * @ORM\JoinColumns({
	 *     @ORM\JoinColumn(name = "commit_repository", referencedColumnName = "repository", nullable = false, onDelete = "CASCADE"),
	 *     @ORM\JoinColumn(name = "commit_sha", referencedColumnName = "sha", nullable = false, onDelete = "CASCADE")
	 * })
	 * @var Commit
	 */
	private $commit;

	/**
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $filename;

	/**
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $status;

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
	private $changes;


	const STATUS_ADDED = 'added';
	const STATUS_MODIFIED = 'modified';
	const STATUS_RENAMED = 'renamed';
	const STATUS_REMOVED = 'removed';


	public function __construct(
		Commit $commit,
		string $filename,
		string $status,
		int $additions,
		int $deletions,
		int $changes
	) {
		$this->id = Uuid::uuid4()->toString();

		$this->commit = $commit;
		$commit->addFile($this);

		$this->filename = $filename;

		$this->status = $status;
		$this->additions = $additions;
		$this->deletions = $deletions;
		$this->changes = $changes;
	}

}
