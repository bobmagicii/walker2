<?php

namespace Walker;

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

		$JobBuffer = NULL;
		$ExtraData = new Common\Datastore;

		($this->Jobs)
		->Each(function(JobFile $Job) use(&$JobBuffer, &$ExtraData) {

			static::DebugLn(sprintf(
				'Running %s',
				$Job->GetFilename()
			));

			// make sure we wont fail in any obvious ways by checking
			// the chain first.

			($Job->Steps)
			->Each(function(JobStep $S) {

				if(!$S->HasClass())
				throw new Common\Error\RequiredDataMissing($S->Class, 'class');

				return;
			});

			// execute the entire chain passing the data from the
			// previous run forwards.

			$StepBuffer = NULL;

			($Job->Steps)
			->Each(function(JobStep $S) use(&$StepBuffer, &$ExtraData) {

				$Inst = $S->NewInstance();
				$StepBuffer = $Inst->Run($StepBuffer, $ExtraData);

				return;
			});

			$JobBuffer = $StepBuffer;

			//static::DebugLn("Final:");
			//Common\Dump::Var($Buffer);

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
