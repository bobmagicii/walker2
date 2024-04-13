<?php

namespace Local;

use Nether\Common;
use Nether\Console;

class JobRunner
extends Common\Prototype {

	#[Common\Meta\PropertyObjectify]
	protected Common\Datastore
	$Jobs;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Add(JobFile $Job):
	static {

		$this->Jobs->Push($Job);

		return $this;
	}

	public function
	Run():
	void {

		$JobCount = $this->Jobs->Count();

		////////

		static::DebugLn(sprintf(
			'%d %s to run.',
			$JobCount,
			Common\Values::IfOneElse($JobCount, 'job', 'jobs')
		));

		////////

		($this->Jobs)
		->Each(function(JobFile $Job) {

			static::DebugLn(sprintf(
				'Running %s',
				$Job->GetFilename()
			));

			return;
		});

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	DebugLn(string $Msg):
	void {

		Console\Util::PrintLn("[Runner] {$Msg}");

		return;
	}

};
