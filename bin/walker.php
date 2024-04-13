<?php

use Nether\Common;
use Nether\Console;

$AppRoot = dirname(__FILE__, 2);
require(sprintf('%s/vendor/autoload.php', $AppRoot));

#[Console\Meta\Application('Walker', '2.0.0-dev')]
class App
extends Nether\Console\Client {

	#[Console\Meta\Command('new')]
	#[Console\Meta\Info('Create a new job json file in the jobs directory.')]
	#[Console\Meta\Arg('name', 'name of this job file')]
	#[Console\Meta\Error(1, 'no job name specified')]
	public function
	HandleNewJob():
	int {

		$Name = Common\Filters\Text::SlottableKey($this->GetInput(1)) ?: NULL;

		if(!$Name)
		$this->Quit(1);

		////////

		$Filename = $this->GetPathToJob($Name);

		$Job = new Local\JobFile;
		$Job->Filename = $Filename;

		$this->PrintBulletList([
			'Job File' => $Job->Filename
		]);

		$Job->Write();

		return 0;
	}

	#[Console\Meta\Command('run')]
	#[Console\Meta\Info('Run a job from the jobs directory.')]
	#[Console\Meta\Arg('name', 'name of this job file')]
	#[Console\Meta\Error(1, 'no job name specified')]
	public function
	HandleRunJob():
	int {

		$Name = Common\Filters\Text::SlottableKey($this->GetInput(1)) ?: NULL;
		$Filename = NULL;
		$Runner = NULL;
		$Job = NULL;
		$Error = NULL;

		////////

		if(!$Name)
		$this->Quit(1);

		////////

		try {
			$Filename = $this->GetPathToJob($Name);
			$Job = Local\JobFile::FromPath($Filename);
		}

		catch(Exception $Error) {
			throw $Error;
		}

		$Runner = new Local\JobRunner;
		$Runner->Add($Job);
		$Runner->Run();

		return 0;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	GetPathToJob(string $Name):
	string {

		$AppRoot = $this->GetOption('AppRoot');

		if(!$AppRoot)
		throw new Common\Error\RequiredDataMissing('AppRoot', 'string');

		////////

		$Filename = Common\Filesystem\Util::Pathify(
			$AppRoot, 'jobs', sprintf('%s.json', $Name)
		);

		////////

		return $Filename;
	}

};

exit(App::Realboot([
	'AppRoot' => $AppRoot
]));
