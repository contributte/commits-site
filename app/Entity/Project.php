<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


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
	private $sort = 0;

	/**
	 * @ORM\OneToMany(targetEntity = "Repository", mappedBy = "project")
	 * @ORM\OrderBy({"name" = "ASC"})
	 * @var Repository[]|Collection
	 */
	private $repositories;

}
