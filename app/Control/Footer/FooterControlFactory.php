<?php

declare(strict_types = 1);

namespace App\Control\Footer;


interface FooterControlFactory
{

	public function create(): FooterControl;

}
