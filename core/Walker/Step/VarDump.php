<?php

namespace Walker\Step;

use Walker;
use Nether\Common;

class VarDump
extends Walker\Step {

	public function
	Run(mixed $Input):
	mixed {

		Common\Dump::Var($Input);

		return $Input;
	}

}
