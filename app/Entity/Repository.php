<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/** @ORM\Entity */
class Repository
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type = "string")
	 * @var string
	 */
	private $id;

	/**
	 * @ORM\Column(type = "string", unique = true)
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\ManyToOne(targetEntity = "Project", inversedBy = "repositories")
	 * @ORM\JoinColumn(nullable = false, onDelete = "CASCADE")
	 * @var Project
	 */
	private $project;

	/**
	 * @ORM\OneToMany(targetEntity = "Commit", mappedBy = "repository")
	 * @ORM\OrderBy({"committedAt" = "DESC"})
	 * @var Commit[]|Collection<int, Commit>
	 */
	private $commits;


	public function __construct(Project $project, string $name)
	{
		$this->name = $name;
		$this->project = $project;
		$this->id = ID::generate();
		$this->commits = new ArrayCollection;
	}


	public function getID(): string
	{
		return $this->id;
	}


	public function getName(): string
	{
		return $this->name;
	}


	public function addCommit(Commit $commit): self
	{
		if (!$this->hasCommit($commit)) {
			$this->commits->add($commit);
		}

		return $this;
	}


	public function hasCommit(Commit $commit): bool
	{
		return $this->commits->contains($commit);
	}

}
