<?php

namespace Local\Seekers;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Local;
use Nether\Browser;
use Nether\Common;

use DOMElement;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class FindUniqueLinksHTML
extends Local\Seeker {

	public function
	Run(mixed $Input):
	mixed {

		// until the attributes are done check that the step before this
		// one generated a Browser Document object.

		if(!($Input instanceof Browser\Document))
		throw new Common\Error\FormatInvalid('Browser\Document');

		////////

		$Links = $Input->Find('a[href]');
		$Found = [];

		$Links->Each(function(DOMElement $E) use(&$Found) {
			$Found[] = $E->GetAttribute('href');
			return;
		});

		$Found = array_unique($Found);
		sort($Found);

		return $Found;
	}

}
