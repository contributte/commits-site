<?php

declare(strict_types = 1);

namespace App\Entity;


class Project
{

	/** @var string */
	private $id;

	/** @var string */
	private $name;


	public function __construct(string $name)
	{
		$this->name = $name;
		$this->id = ID::generate();
	}

}
