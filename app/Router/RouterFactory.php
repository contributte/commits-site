<?php

declare(strict_types = 1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use App\QueryFunction\Project\ProjectsSortedQuery;
use Doctrine\DBAL\Exception\TableNotFoundException;


final class RouterFactory
{

	/** @var ProjectsSortedQuery */
	private $projectsQuery;


	public function __construct(ProjectsSortedQuery $projectsQuery)
	{
		$this->projectsQuery = $projectsQuery;
	}


	public function createRouter(): Nette\Application\IRouter
	{
		$router = new RouteList;

		try {
			$projectSlugs = [];
			foreach ($this->projectsQuery->get() as $project) {
				$projectSlugs[] = $project->getSlug();
			}

			if (count($projectSlugs)) {
				$router[] = new Route('[<projectSlug=' . reset($projectSlugs) . ' ' . implode('|', $projectSlugs) . '>]', 'Project:commits');

			} else {
				$router[] = new Route('', function () {
					echo 'ERROR: No project found. Did you run fixtures?';
					exit(1);
				});
			}

		} catch (TableNotFoundException $e) { // schema may not exist yet
			$router[] = new Route('', function () {
				echo 'ERROR: Database schema does not exist. Have you created it?';
				exit(1);
			});
		}

		return $router;
	}

}
