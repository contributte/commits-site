<?php

declare(strict_types = 1);

namespace App\Control\Grid\CommitsGrid;

use App\Entity\Project;


interface ICommitsGridFactory
{

	public function create(Project $project): CommitsGrid;

}
