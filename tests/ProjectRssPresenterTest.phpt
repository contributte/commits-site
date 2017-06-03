<?php

declare(strict_types = 1);

namespace Tests;

use Nette\Application\IPresenterFactory;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/PresenterTestCase.php';


final class ProjectRssPresenterTest extends PresenterTestCase
{

	public function getPresenterName(): string
	{
		return 'ProjectRss';
	}


	/** @dataProvider provideProjectSlugs */
	public function testActionCommits(string $projectSlug): void
	{
		$this->assertTextResponse('GET', [
			'action' => 'feed',
			'projectSlug' => $projectSlug,
		]);
	}


	public function provideProjectSlugs(): array
	{
		return [
			['docs'],
			['framework'],
			['promo'],
			['tester'],
			['tools'],
		];
	}

}


/** @var IPresenterFactory $presenterFactory */
$presenterFactory = Bootstrap::boot()->createContainer()->getByType(IPresenterFactory::class);

(new ProjectRssPresenterTest($presenterFactory))->run();
