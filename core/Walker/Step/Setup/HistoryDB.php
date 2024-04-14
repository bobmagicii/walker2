<?php

namespace Walker\Step\Setup;

use Walker;
use Nether\Common;

class HistoryDB
extends Walker\Step {

	public function
	Run(mixed $In, Common\Datastore $Ex):
	mixed {

		$LinkLogger = new Walker\History\Links($this->Runner->App);

		return $In;
	}

};
