<?php

namespace Walker;

use Nether\Common;

abstract class Step {

	protected
	$Runner;

	public function
	SetRunner(JobRunner $Runner):
	static {

		$this->Runner = $Runner;
		return $this;
	}

	abstract public function
	Run(mixed $Input, Common\Datastore $ExtraData): mixed;

};
