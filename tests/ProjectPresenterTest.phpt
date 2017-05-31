<?php

declare(strict_types = 1);

namespace Tests;

use Nette\Application\IPresenterFactory;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/PresenterTestCase.php';


final class ProjectPresenterTest extends PresenterTestCase
{

	public function getPresenterName(): string
	{
		return 'Project';
	}


	/** @dataProvider provideProjectSlugs */
	public function testActionCommits(string $projectSlug): void
	{
		$this->assertTextResponse('GET', [
			'action' => 'commits',
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

(new ProjectPresenterTest($presenterFactory))->run();
