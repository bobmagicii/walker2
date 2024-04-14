<?php

namespace Walker\Step\Filter;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Walker;
use Nether\Browser;
use Nether\Common;
use Nether\Console;

use DOMElement;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class CheckLinksForText
extends Walker\Step {

	public int
	$Delay;

	public ?string
	$Text;

	public function
	__Construct(?string $Text, int $Delay=0) {

		$this->Text = $Text;
		$this->Delay = $Delay;

		return;
	}

	public function
	Run(mixed $Input, Common\Datastore $ExtraData):
	mixed {

		// until the attributes are done check that the step before

		if(!($Input instanceof Common\Datastore))
		throw new Common\Error\FormatInvalid('datastore of urls');

		////////

		$Output = Common\Datastore::FromArray($Input);

		$Output->Filter(function(string $URL) {

			static::DebugLn($URL);

			$Client = Browser\Client::FromURL($URL);
			$Page = $Client->FetchAsHTML();
			$Text = $Page->Text();
			$Find = preg_quote($this->Text, '#');

			if(!preg_match("#\b{$Find}\b#ms", $Text))
			return FALSE;

			return TRUE;
		});

		static::DebugLn(sprintf(
			'Found %d link(s) with `%s` on it.',
			$Output->Count(),
			$this->Text
		));

		////////

		return $Output->GetData();
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	DebugLn(string $Msg):
	void {

		Console\Util::PrintLn("[CheckLinksForText] {$Msg}");
		return;
	}

}
