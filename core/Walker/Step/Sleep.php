<?php

namespace Walker\Step;

use Walker;
use Nether\Common;

class Sleep
extends Walker\Step {

	public function
	Run(mixed $Input, Common\Datastore $ExtraData):
	mixed {

		if(!is_numeric($Input))
		throw new Common\Error\FormatInvalid('num of sec to sleep');

		sleep((int)$Input);

		return $Input;
	}

}
