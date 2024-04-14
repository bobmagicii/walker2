<?php

namespace Local\Seekers;

use Local;
use Nether\Browser;
use Nether\Common;
use Nether\Console;

use DOMElement;

class FindElementHTML
extends Local\Seeker {

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

		/** @var Browser\Document $Input */

		return $Input->Find($this->Selector);

		return $Input;
	}

}
