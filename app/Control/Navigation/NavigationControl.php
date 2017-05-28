<?php

declare(strict_types = 1);

namespace App\Control\Navigation;

use Nette\Application\UI\Control;
use App\QueryFunction\Project\ProjectsSortedQuery;


final class NavigationControl extends Control
{

	/** @var ProjectsSortedQuery */
	private $projectsQuery;


	public function __construct(ProjectsSortedQuery $projectsQuery)
	{
		parent::__construct();

		$this->projectsQuery = $projectsQuery;
	}


	public function render(): void
	{
		$template = $this->createTemplate();
		$template->setFile(__DIR__ . '/NavigationControl.latte');
		$template->projects = $this->projectsQuery->get();
		$template->render();
	}

}
