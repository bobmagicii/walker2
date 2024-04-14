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

	public float
	$Drift;

	public ?string
	$Text;

	public function
	__Construct(?string $Text, int $Delay=0, float $Drift=0.2) {

		$this->Text = $Text;
		$this->Delay = $Delay;
		$this->Drift = $Drift;

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

		$Output->Filter(function(string $URL) use($ExtraData) {

			if($this->Delay) {
				$Delay = $this->Delay;

				if($Delay < 0)
				$Delay = abs(random_int(
					floor($Delay * (1.0 + $this->Drift)),
					ceil($Delay * (1.0 - $this->Drift))
				));

				static::DebugLn("{$URL} ({$Delay}s)");
				sleep($Delay);
			}

			else {
				static::DebugLn($URL);
			}

			////////

			$DKey = "Document({$URL})";

			if(!$ExtraData[$DKey]) {
				$Client = Browser\Client::FromURL($URL);
				$ExtraData[$DKey] = $Client->FetchAsHTML();
			}

			$Text = $ExtraData[$DKey]->Text();
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

		return $Output;
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
