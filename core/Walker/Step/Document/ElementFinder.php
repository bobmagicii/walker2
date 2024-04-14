<?php

namespace Walker\Step\Document;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Walker;
use Nether\Browser;
use Nether\Common;
use Nether\Console;

use DOMElement;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Common\Meta\Info('Find all the elements in a document.')]
class ElementFinder
extends Walker\Step {

	public string
	$Selector;

	public function
	__Construct(string $Selector) {

		$this->Selector = $Selector;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Run(mixed $Input, Common\Datastore $ExtraData):
	mixed {

		// until the attributes are done check that the step before this
		// one generated a Browser Document object.

		if(!($Input instanceof Browser\Document))
		throw new Common\Error\FormatInvalid('Browser\Document');

		////////

		$Found = new Common\Datastore;

		$Input
		->Find($this->Selector)
		->Each(function(DOMElement $E) use($Found) {
			$Found->Push($E);
			return;
		});

		return $Found;
	}

}
