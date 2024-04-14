<?php

namespace Walker\Step\History;

use Walker;
use Nether\Common;
use Nether\Console;

class LogTheseLinks
extends Walker\Step {

	public string
	$Status;

	public function
	__Construct(string $Status='logged') {

		$this->Status = $Status;

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Run(mixed $Input, Common\Datastore $Extra):
	mixed {

		if(!($Input instanceof Common\Datastore))
		throw new Common\Error\FormatInvalid('datastore of urls');

		////////

		$Logger = new Walker\History\Links($this->Runner->App);

		$Input->Each(function(string $URL) use($Logger, $Extra) {
			$Logger->Add($URL, $Extra['Job.Name'], $this->Status);
			return;
		});

		return $Input;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	DebugLn(string $Msg):
	void {

		Console\Util::PrintLn("[LinkLogger] {$Msg}");
		return;
	}


}
