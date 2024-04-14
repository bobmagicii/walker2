<?php

namespace Walker\Step\Document;

use Walker;
use Nether\Browser;
use Nether\Common;
use Nether\Console;

#[Common\Meta\Info('Consume a URL as HTML and return the Document object.')]
class FetchHTML
extends Walker\Step {

	public int
	$Page;

	public string
	$URL;

	public function
	__Construct(string $URL, int $Page=1) {

		$this->URL = $URL;
		$this->Page = $Page;

		return;
	}

	public function
	Run(mixed $Input, Common\Datastore $ExtraData):
	mixed {

		$ExtraData->Define('Page', $this->Page);

		////////

		$URL = $this->TransformURL($ExtraData);
		$ExtraData->Set('URL', $URL);
		static::DebugLn($URL);

		////////

		$Client = new Browser\Client([ 'URL'=> $URL ]);
		$Output = $Client->FetchAsHTML();

		////////

		return $Output;
	}

	protected function
	TransformURL(Common\Datastore $Extra):
	string {

		$URL = $this->URL;
		$URL = str_replace('%Page%', $Extra['Page'], $URL);

		return $URL;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	DebugLn(string $Msg):
	void {

		Console\Util::PrintLn("[FetchHTML] {$Msg}");

		return;
	}

}
