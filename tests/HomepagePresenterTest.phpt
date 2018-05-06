<?php

declare(strict_types = 1);

namespace Tests;

use Nette\Application\IPresenterFactory;

require_once __DIR__ . '/Bootstrap.php';
require_once __DIR__ . '/PresenterTestCase.php';


final class HomepagePresenterTest extends PresenterTestCase
{

	public function getPresenterName(): string
	{
		return 'Homepage';
	}


	public function testActionDefault(): void
	{
		$this->assertTextResponse('GET', [
			'action' => 'default',
		]);
	}

}


/** @var IPresenterFactory $presenterFactory */
$presenterFactory = Bootstrap::boot()->createContainer()->getByType(IPresenterFactory::class);

(new HomepagePresenterTest($presenterFactory))->run();
