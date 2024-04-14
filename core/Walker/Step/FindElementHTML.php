<?php

namespace Walker\Step;

use Walker;
use Nether\Browser;
use Nether\Common;

class FindElementHTML
extends Walker\Step {

	public string
	$Selector;

	public function
	__Construct(string $Selector) {

		$this->Selector = $Selector;
		return;
	}

	public function
	Run(mixed $Input):
	mixed {

		if(!($Input instanceof Browser\Document))
		throw new Common\Error\FormatInvalid('Browser\Document');

		return $Input->Find($this->Selector);
	}

}
