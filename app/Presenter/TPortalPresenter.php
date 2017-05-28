<?php

declare(strict_types = 1);

namespace App\Presenter;

use App\Control\Navigation\NavigationControl;
use App\Control\Navigation\INavigationControlFactory;


trait TPortalPresenter
{

	/** @var INavigationControlFactory @inject */
	public $navigationControlFactory;


	protected function createComponentNavigation(): NavigationControl
	{
		return $this->navigationControlFactory->create();
	}

}
