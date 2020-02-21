<?php

declare(strict_types = 1);

namespace App\Form\Controls;

use Nette\Forms\Form;
use Nette\Forms\Controls\TextInput;


final class RawTextInput extends TextInput
{

	/** @inheritdoc */
	public function loadHttpData(): void
	{
		$this->setValue($this->getHttpData(Form::DATA_TEXT)); // intentionally DATA_TEXT not to omit leading/trailing spaces
	}


	/** @inheritdoc */
	public function getValue()
	{
		$value = $this->value === $this->translate($this->emptyValue) ? '' : $this->value;
		return $value === '' ? null : $value;
	}

}
