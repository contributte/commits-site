<?php

declare(strict_types = 1);

namespace App\Entity;


class Repository
{

	/** @var string */
	private $id;

	/** @var Project */
	private $project;

	/** @var string */
	private $name;


	public function __construct(Project $project, string $name)
	{
		$this->name = $name;
		$this->project = $project;
		$this->id = ID::generate();
	}

}
