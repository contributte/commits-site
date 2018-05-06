<?php

declare(strict_types = 1);

namespace App\Control\Grid\CommitsGrid;

use TwiGrid\DataGrid;
use App\Entity\Commit;
use App\Entity\Project;
use Nette\Forms\Container;
use TwiGrid\Components\Column;
use TwiGrid\Components\Translator;
use App\Form\Controls\RawTextInput;
use Nette\Application\UI\ITemplate;
use App\Repository\CommitRepository;
use App\QueryFunction\Commit\CommitsFilteredByProjectQuery;
use App\QueryFunction\Commit\CommitsFilteredByProjectCountQuery;


final class CommitsGrid extends DataGrid
{

	/** @var Project */
	private $project;

	/** @var CommitsFilteredByProjectCountQuery */
	private $commitsCountQuery;

	/** @var CommitsFilteredByProjectQuery */
	private $commitsFilteredQuery;


	public function __construct(
		Project $project,
		CommitsFilteredByProjectCountQuery $commitsCountQuery,
		CommitsFilteredByProjectQuery $commitsFilteredQuery

	) {
		parent::__construct();

		$this->project = $project;
		$this->commitsCountQuery = $commitsCountQuery;
		$this->commitsFilteredQuery = $commitsFilteredQuery;
	}


	protected function createTemplate(): ITemplate
	{
		$template = parent::createTemplate();

		$template->project = $this->project;
		$this->redrawControl('filters-toggler');

		return $template;
	}


	protected function build(): void
	{
		$this->setPrimaryKey(['repository', 'sha']);
		$this->setTemplateFile(__DIR__ . '/CommitsGrid.latte');
		$this->setRecordVariable('commit');

		$this->setTranslator(new Translator([
			'twigrid.data.no_data' => 'No commits found for this filter.',
		]));

		if ($this->project->hasMultipleRepositories()) {
			$this->addColumn('repository', 'Repository');
		}

		$this->addColumn('author', 'Author');
		$this->addColumn('message', 'Commit message');
		$this->addColumn('committed_at', 'Committed')->setSortable(true);
		$this->addColumn('sha', 'SHA');

		$this->setMultiSort(false);
		$this->setDefaultOrderBy('committed_at', Column::DESC);

		$this->setFilterFactory(function (Container $c) {
			if ($this->project->hasMultipleRepositories()) {
				$repositories = [];
				foreach ($this->project->getRepositories() as $repository) {
					$repositories[$repository->getName()] = $repository->getBasename();
				}

				$c->addSelect('repository', null, $repositories)
					->setPrompt('- all -');
			}

			$c->addText('author');
			$c['message'] = new RawTextInput();
			$c->addText('sha');
		});

		$this->setValueGetter(function (Commit $commit, $name) {
			switch ($name) {
				case 'repository':
					return $commit->getRepository()->getBasename();

				case 'sha':
					return $commit->getSha();
			}
		});

		$this->setPagination(21, function (array $filters) {
			return $this->commitsCountQuery->get($this->project, $filters);
		});

		$this->setDataLoader(function (array $filters, array $orderBy, $offset, $limit) {
			return $this->commitsFilteredQuery->get($this->project, $filters, $orderBy, $offset, $limit);
		});

		$this->addRowAction('browse_tree', '', function () {});
	}

}
