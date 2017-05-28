<?php

declare(strict_types = 1);

namespace Tests;

use Nette\Application\IPresenterFactory;

require_once __DIR__ . '/Bootstrap.php';
require_once __DIR__ . '/PresenterTestCase.php';


final class ProjectPresenterTest extends PresenterTestCase
{

	public function getPresenterName(): string
	{
		return 'Project';
	}


	public function testActionDefault(): void
	{
		$this->assertTextResponse('GET', [
			'action' => 'commits',
		]);
	}

}

/** @var IPresenterFactory $presenterFactory */
$presenterFactory = Bootstrap::boot()->createContainer()->getByType(IPresenterFactory::class);

(new ProjectPresenterTest($presenterFactory))->run();
