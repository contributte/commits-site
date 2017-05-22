<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(indexes = {
 *     @ORM\Index(columns = {"sort"})
 * })
 */
class Project
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
	 * @ORM\Column(type = "string", unique = true)
	 * @var string
	 */
	private $slug;

	/**
	 * @ORM\Column(type = "integer")
	 * @var int
	 */
	private $sort;

	/**
	 * @ORM\OneToMany(targetEntity = "Repository", mappedBy = "project")
	 * @ORM\OrderBy({"name" = "ASC"})
	 * @var Repository[]|Collection<int, Repository>
	 */
	private $repositories;


	public function __construct(string $name, string $slug, int $sort = 0)
	{
		$this->name = $name;
		$this->slug = $slug;
		$this->sort = $sort;
		$this->id = ID::generate();
		$this->repositories = new ArrayCollection;
	}

}
