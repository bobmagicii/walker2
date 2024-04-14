<?php

namespace Local;

use Nether\Common;

class JobSeeker
extends Common\Prototype {

	#[Common\Meta\PropertyListable]
	public ?string
	$Class = NULL;

	#[Common\Meta\PropertyFactory('FromArray')]
	#[Common\Meta\PropertyListable]
	public array|Common\Datastore
	$Args = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	HasClass():
	bool {

		return class_exists($this->Class);
	}

	public function
	NewInstance():
	object {

		return new ($this->Class)(...$this->Args->GetData());
	}

};
