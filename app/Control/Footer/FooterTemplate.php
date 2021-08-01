<?php

declare(strict_types = 1);

namespace App\Control\Footer;

use App\View\DefaultTemplate;
use App\Entity\Synchronization\SynchronizationLog;


final class FooterTemplate extends DefaultTemplate
{

	public ?SynchronizationLog $lastSynchronizationLog;

}
