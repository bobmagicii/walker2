<?php

namespace Local\Seekers;

use Local;
use Nether\Common;
use Nether\Console;

class VarDump
extends Local\Seeker {

	public function
	Run(mixed $Input):
	mixed {

		Common\Dump::Var($Input);

		return $Input;
	}

}
