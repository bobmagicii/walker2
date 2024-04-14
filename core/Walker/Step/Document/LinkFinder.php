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

#[Common\Meta\Info('Find all the unique hyperlinks in a document and return a datastore of URLs.')]
class LinkFinder
extends Walker\Step {

	public bool
	$Unique;

	public function
	__Construct(bool $Unique=TRUE) {

		$this->Unique = $Unique;

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

		$Links = $Input->Find('a[href]');
		$Found = new Common\Datastore;

		($Links)
		->Each(function(DOMElement $E) use($Found) {
			$Found->Push($E->GetAttribute('href'));
			return;
		});

		($Found)
		->Flatten()
		->Remap(function(string $URL) use($ExtraData) {

			// the double-slash notation where the browser fills in where
			// ever they want to be.

			if(str_starts_with($URL, '//'))
			return sprintf('https:%s', $URL);

			// absolute path notation where the prefix is glued on.

			if(str_starts_with($URL, '/'))
			return sprintf(
				'%s/%s',
				static::Deburr($ExtraData['URL']),
				static::Deburr($URL)
			);

			// ...

			if(!preg_match('#^(?:.+?):\/\/#', $URL))
			return sprintf(
				'%s/%s',
				static::Deburr($ExtraData['URL']),
				static::Deburr($URL)
			);

			return $URL;
		});

		return $Found;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	Deburr(string $In):
	string {

		return trim($In, '/');
	}

}
