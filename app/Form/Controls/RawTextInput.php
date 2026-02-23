<?php

declare(strict_types = 1);

namespace App\Form\Controls;

use Nette\Forms\Form;
use Nette\Forms\Controls\TextInput;


final class RawTextInput extends TextInput
{

	public function loadHttpData(): void
	{
		$this->setValue($this->getHttpData(Form::DataText)); // intentionally DataText not to omit leading/trailing spaces
	}


	public function getValue(): ?string
	{
		$value = $this->value === $this->translate($this->emptyValue) ? '' : $this->value;
		assert($value === null || is_string($value));
		return $value === '' ? null : $value;
	}

}
