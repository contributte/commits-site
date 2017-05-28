<?php

declare(strict_types = 1);

namespace App\Presenter;

use Nette;


/** @property Nette\Bridges\ApplicationLatte\Template $template */
final class Error4xxPresenter extends Nette\Application\UI\Presenter
{

	use TPortalPresenter;


	public function startup(): void
	{
		parent::startup();

		$request = $this->getRequest();

		if (!$request || !$request->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}


	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
		// load template 403.latte or 404.latte or ... 4xx.latte
		$file = __DIR__ . "/templates/Error/{$exception->getCode()}.latte";
		$this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte');
	}

}
