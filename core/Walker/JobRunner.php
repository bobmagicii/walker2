<?php

namespace Walker;

use Nether\Common;
use Nether\Console;

class JobRunner
extends Common\Prototype {

	protected TerminalApp
	$App;

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
		$ExtraData = Common\Datastore::FromArray([
			'Job.Name'      => NULL,
			'Job.Repeat'    => FALSE,
			'Job.Iteration' => 0
		]);

		($this->Jobs)
		->Each(function(JobFile $Job) use(&$JobBuffer, &$ExtraData) {

			$RunThisJob = TRUE;

			while($RunThisJob) {
				$ExtraData['Job.Name'] = basename($Job->Filename);
				$ExtraData['Job.Iteration'] += 1;

				static::DebugLn(sprintf(
					'Running %s (Iter: 1)',
					$Job->GetFilename(),
					$ExtraData['Job.Iteration']
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
					$Inst->SetRunner($this);

					$StepBuffer = $Inst->Run(
						$StepBuffer,
						$ExtraData
					);

					return;
				});

				$RunThisJob = $ExtraData['Job.Repeat'] ?: FALSE;
				$JobBuffer = $StepBuffer;
			}

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
