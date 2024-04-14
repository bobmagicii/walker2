<?php

namespace Walker;

use Nether\Common;

abstract class Step {

	abstract public function
	Run(mixed $Input, Common\Datastore $ExtraData): mixed;

};
