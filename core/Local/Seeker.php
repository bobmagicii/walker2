<?php

namespace Local;

use Nether\Common;

class Seeker
extends Common\Prototype {

	protected mixed
	$Content;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	SetContent(mixed $Content):
	static {

		$this->Content = $Content;

		return $this;
	}

	public function
	GetContent():
	mixed {

		return $this->Content;
	}

};
