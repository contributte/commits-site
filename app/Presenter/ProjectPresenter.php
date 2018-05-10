<?php

declare(strict_types = 1);

namespace App\Presenter;

use App\Entity\Project;
use Nette\Http\Request;
use Nette\Application\UI\Presenter;
use App\Control\Grid\CommitsGrid\CommitsGrid;
use App\Control\Grid\CommitsGrid\ICommitsGridFactory;
use App\QueryFunction\Project\ProjectNotFoundException;
use App\QueryFunction\Project\ProjectBySlugWithRepositoriesQuery;


final class ProjectPresenter extends Presenter
{

	use TPortalPresenter;


	/** @var ProjectBySlugWithRepositoriesQuery */
	private $projectBySlugQuery;

	/** @var Request */
	private $httpRequest;

	/** @var ICommitsGridFactory */
	private $gridFactory;

	/** @var Project */
	private $project;


	public function __construct(ProjectBySlugWithRepositoriesQuery $projectBySlugQuery, Request $httpRequest, ICommitsGridFactory $gridFactory)
	{
		parent::__construct();

		$this->httpRequest = $httpRequest;
		$this->gridFactory = $gridFactory;
		$this->projectBySlugQuery = $projectBySlugQuery;
	}


	public function actionCommits(string $projectSlug): void
	{
		try {
			$this->project = $this->projectBySlugQuery->get($projectSlug);

		} catch (ProjectNotFoundException $e) {
			$this->error($e->getMessage());
		}
	}


	public function renderCommits(string $projectSlug): void
	{
		$referrer = $this->httpRequest->getReferer();

		if ($referrer && $referrer->getHost() === 'nette-commits.cz') {
			$this->template->urlChangeWarning = true;
		}

		$this->template->project = $this->project;
	}


	protected function createComponentCommitsGrid(): CommitsGrid
	{
		return $this->gridFactory->create($this->project);
	}

}
