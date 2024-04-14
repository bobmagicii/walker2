<?php

namespace Walker\Step;

use Walker;
use Nether\Common;

class Dump
extends Walker\Step {

	public function
	Run(mixed $Input, Common\Datastore $ExtraData):
	mixed {

		Common\Dump::Var($Input);

		return $Input;
	}

}
