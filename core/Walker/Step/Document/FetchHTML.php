<?php

namespace Walker\Step\Document;

use Walker;
use Nether\Browser;
use Nether\Common;

#[Common\Meta\Info('Consume a URL as HTML and return the Document object.')]
class FetchHTML
extends Walker\Step {

	public string
	$URL;

	public function
	__Construct(string $URL) {

		$this->URL = $URL;

		return;
	}

	public function
	Run(mixed $Input, Common\Datastore $ExtraData):
	mixed {

		$Client = new Browser\Client([ 'URL'=> $this->URL ]);
		$ExtraData['URL'] = $this->URL;

		$Output = $Client->FetchAsHTML();

		return $Output;
	}

}
