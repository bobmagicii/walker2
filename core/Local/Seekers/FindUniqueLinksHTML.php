<?php

namespace Local\Seekers;

use Local;
use Nether\Browser;
use Nether\Common;
use Nether\Console;

use DOMElement;

class FindUniqueLinksHTML
extends Local\Seeker {

	public function
	Run(mixed $Input):
	mixed {

		if(!($Input instanceof Browser\Document))
		throw new Common\Error\FormatInvalid('Browser\Document');

		$Found = [];
		$Links = $Input->Find('a[href]');

		$Links->Each(function(DOMElement $E) use(&$Found) {
			$URL = $E->GetAttribute('href');
			$Found[$URL] = TRUE;
			return;
		});

		$Found = array_keys($Found);
		sort($Found);

		return $Found;
	}

}
