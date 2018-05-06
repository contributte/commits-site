<?php

declare(strict_types = 1);

namespace Tests;

use Tester\Assert;
use Tester\TestCase;
use Nette\Application\Request;
use Nette\Application\IPresenter;
use Nette\Application\UI\Presenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\Responses\TextResponse;


abstract class PresenterTestCase extends TestCase
{

	/** @var IPresenterFactory */
	private $presenterFactory;

	/** @var IPresenter */
	private $presenter;


	public function __construct(IPresenterFactory $presenterFactory)
	{
		$this->presenterFactory = $presenterFactory;
	}


	abstract public function getPresenterName(): string;


	protected function setUp(): void
	{
		/** @var Presenter $presenter */
		$presenter = $this->presenterFactory->createPresenter($this->getPresenterName());

		$presenter->autoCanonicalize = false;
		$this->presenter = $presenter;
	}


	/** @param  mixed[] $params */
	final public function assertTextResponse(string $method, array $params): void
	{
		$request = new Request($this->getPresenterName(), $method, $params);
		$response = $this->presenter->run($request);
		Assert::type(TextResponse::class, $response);
	}

}
