<?php

declare(strict_types = 1);

namespace App\Control\Navigation;


interface INavigationControlFactory
{

	public function create(): NavigationControl;

}
