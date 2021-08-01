<?php

declare(strict_types = 1);

namespace App\Control\Footer;

use Nette\Application\UI\Control;
use App\QueryFunction\Synchronization\SynchronizationLogLatestQuery;
use App\QueryFunction\Synchronization\SynchronizationLogNotFoundException;


final class FooterControl extends Control
{

	private SynchronizationLogLatestQuery $syncLogQuery;


	public function __construct(SynchronizationLogLatestQuery $syncLogQuery)
	{
		$this->syncLogQuery = $syncLogQuery;
	}


	public function render(): void
	{
		/** @var FooterTemplate $template */
		$template = $this->createTemplate(FooterTemplate::class);

		try {
			$template->lastSynchronizationLog = $this->syncLogQuery->get();

		} catch (SynchronizationLogNotFoundException $e) {
			$template->lastSynchronizationLog = null;
		}

		$template->setFile(__DIR__ . '/FooterControl.latte');
		$template->render();
	}

}
