<?php

namespace Walker\Step\Filter;

use Walker;
use Nether\Common;
use Nether\Console;

class SkipLoggedLinks
extends Walker\Step {

	public function
	Run(mixed $Input, Common\Datastore $Extra):
	mixed {

		if(!($Input instanceof Common\Datastore))
		throw new Common\Error\FormatInvalid('datastore of urls');

		////////

		$Logger = new Walker\History\LinkLogger;

		$Output = $Input->Distill(function(string $URL) use($Logger, $Extra) {
			return !$Logger->Has($URL, $Extra['Job.Name']);
		});

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	DebugLn(string $Msg):
	void {

		Console\Util::PrintLn("[SkipLinksAlreadyLogged] {$Msg}");

		return;
	}

};
