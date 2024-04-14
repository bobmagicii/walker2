<?php

namespace Walker;

use Nether\Browser;
use Nether\Common;
use Nether\Console;
use Nether\Database;

use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Console\Meta\Application('Walker', '2.0.0-dev', Phar: 'walker.phar')]
class TerminalApp
extends Console\Client {

	protected string
	$AppRoot;

	protected string
	$BootRoot;

	protected string
	$JobRoot;

	protected Common\Datastore
	$Config;

	protected Common\Datastore
	$Library;

	protected Database\Manager
	$DB;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnPrepare():
	void {

		$this->AppRoot = $this->GetOption('AppRoot');
		$this->BootRoot = $this->GetOption('BootRoot');

		$this->Config = new Common\Datastore;
		$this->Library = new Common\Datastore;

		return;
	}

	protected function
	OnReady():
	void {

		($this->Library)
		->Shove('Browser', new Browser\Library($this->Config))
		->Shove('Database', new Database\Library($this->Config));

		////////

		$this->DB = new Database\Manager;
		$this->DB->Add(new Database\Connection\SQLite(
			Name: 'History',
			Database: Common\Filesystem\Util::Pathify(
				$this->AppRoot,
				Common\Filters\Text::SlottableKey($this->AppInfo->Name),
				'history.sqlite'
			)
		));

		////////

		$this->JobRoot = match(TRUE) {
			($this->HasOption('JobRoot'))
			=> $this->GetOption('JobRoot'),

			default
			=> Common\Filesystem\Util::Pathify(
				$this->AppRoot,
				Common\Filters\Text::SlottableKey($this->AppInfo->Name),
				'jobs'
			)
		};

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Console\Meta\Command('config')]
	#[Console\Meta\Info('Show/Edit configuration settings.')]
	public function
	HandleInfo():
	int {

		$Browser = Browser\Client::FromURL('http://pegasusgate.net');

		$this
		->PrintAppHeader('Config')
		->PrintBulletList([
			'UserAgent' => $Browser->GetUserAgent()
		]);

		return 0;
	}

	#[Console\Meta\Command('new')]
	#[Console\Meta\Info('Create a new job json file in the jobs directory.')]
	#[Console\Meta\Arg('name', 'name of this job file')]
	#[Console\Meta\Error(1, 'no job name specified')]
	public function
	HandleNewJob():
	int {

		$this->PrintAppHeader('New Job File');

		$Name = Common\Filters\Text::SlottableKey($this->GetInput(1)) ?: NULL;

		if(!$Name)
		$this->Quit(1);

		////////

		$Filename = $this->GetPathToJob($Name);

		$Job = new JobFile;
		$Job->Filename = $Filename;

		$this->PrintBulletList([
			'Job File' => $Job->Filename
		]);

		$Job->Write();

		return 0;
	}

	#[Console\Meta\Command('rehash')]
	#[Console\Meta\Info('Read a job file and immediately resave it.')]
	#[Console\Meta\Arg('name', 'name of this job file')]
	#[Console\Meta\Error(1, 'no job name specified')]
	#[Console\Meta\Error(2, 'Job %s')]
	public function
	HandleRehash():
	int {

		$this->PrintAppHeader('Rehash Job File');

		$Name = Common\Filters\Text::SlottableKey($this->GetInput(1)) ?: NULL;

		if(!$Name)
		$this->Quit(1);

		////////

		try {
			$Filename = $this->GetPathToJob($Name);
			$Job = JobFile::FromPath($Filename);
		}

		catch(Exception $Error) {
			$this->Quit(2, $Error->GetMessage());
		}

		////////

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
	#[Console\Meta\Error(2, 'Job %s')]
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

		$this->PrintAppHeader("Run {$Name}");

		try {
			$Filename = $this->GetPathToJob($Name);
			$Job = JobFile::FromPath($Filename);
		}

		catch(Exception $Error) {
			$this->Quit(2, $Error->GetMessage());
		}

		$Runner = new JobRunner([ 'App'=> $this ]);
		$Runner->Add($Job);
		$Runner->Run();

		return 0;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	GetPathToJob(string $Name):
	string {

		$Filename = Common\Filesystem\Util::Pathify(
			$this->JobRoot, sprintf('%s.json', $Name)
		);

		////////

		return $Filename;
	}

	protected function
	GetPharFiles():
	Common\Datastore {

		$Index = parent::GetPharFiles();
		$Index->Push('core');

		return $Index;
	}

	protected function
	GetPharFileFilters():
	Common\Datastore {

		$Filters = parent::GetPharFileFilters();

		$Filters->Push(function(string $File) {

			$DS = DIRECTORY_SEPARATOR;

			// dev deps that dont need to be.

			if(str_contains($File, "squizlabs{$DS}"))
			return FALSE;

			if(str_contains($File, "dealerdirect{$DS}"))
			return FALSE;

			if(str_contains($File, "netherphp{$DS}standards"))
			return FALSE;

			// unused deps from Nether\Common that dont need to be.

			if(str_contains($File, "monolog{$DS}"))
			return FALSE;

			// unused deps from Nether\Database that dont need to be.

			if(str_contains($File, "cakephp{$DS}"))
			return FALSE;

			if(str_contains($File, "phelium{$DS}"))
			return FALSE;

			if(str_contains($File, "robmorgan{$DS}"))
			return FALSE;

			if(str_contains($File, "symfony{$DS}console"))
			return FALSE;

			////////

			return TRUE;
		});

		return $Filters;
	}

};
