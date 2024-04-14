<?php

namespace Local\Seekers;

use Local;
use Nether\Browser;
use Nether\Common;

class RemoteDocumentHTML
extends Local\Seeker {

	public string
	$URL;

	public function
	__Construct(string $URL) {

		$this->URL = $URL;

		return;
	}

	public function
	Run(mixed $Input):
	mixed {

		$Client = new Browser\Client([
			'URL' => $this->URL
		]);

		$Output = $Client->FetchAsHTML();

		return $Output;
	}

}
