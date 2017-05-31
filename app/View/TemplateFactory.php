<?php

declare(strict_types = 1);

namespace App\View;

use Nette\Http\IRequest;
use Nette\Security\User;
use Nette\Caching\IStorage;
use App\Helper\FileMtimeHelper;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use App\Helper\TimeAgoInWordsHelper;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\ILatteFactory;


final class TemplateFactory extends \Nette\Bridges\ApplicationLatte\TemplateFactory
{

	/** @var FileMtimeHelper */
	private $fileMtimeHelper;


	public function __construct(
		ILatteFactory $latteFactory,
		IRequest $httpRequest = null,
		User $user = null,
		IStorage $cacheStorage = null,
		string $templateClass = null,
		FileMtimeHelper $fileMtimeHelper

	) {
		parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage, $templateClass);

		$this->fileMtimeHelper = $fileMtimeHelper;
	}


	public function createTemplate(Control $control = null): ITemplate
	{
		/** @var Template $template */
		$template = parent::createTemplate($control);

		$template->getLatte()
			->addFilter('mtime', function (string $relPath): string {
				return sprintf('%s?%d', $relPath, $this->fileMtimeHelper->getMtime($relPath));
			})

			->addFilter('timeAgoInWords', static function ($time): ?string {
				return TimeAgoInWordsHelper::convert($time);
			})
		;

		return $template;
	}

}
