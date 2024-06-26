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

	public string
	$AppRoot;

	public string
	$BootRoot;

	public string
	$JobRoot;

	public string
	$DBRoot;

	public Common\Datastore
	$Config;

	public Common\Datastore
	$Library;

	public Database\Manager
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

		////////

		$this->DBRoot = match(TRUE) {
			($this->HasOption('DBRoot'))
			=> $this->GetOption('DBRoot'),

			default
			=> Common\Filesystem\Util::Pathify(
				$this->AppRoot,
				Common\Filters\Text::SlottableKey($this->AppInfo->Name),
				'dbs'
			)
		};

		if(!is_dir($this->DBRoot))
		Common\Filesystem\Util::MkDir($this->DBRoot);

		$this->DB = new Database\Manager;

		////////

		$this->DB->Add(new Database\Connection\SQLite(
			Name: History\LinkEntity::$DBA,
			Database: Common\Filesystem\Util::Pathify(
				$this->DBRoot, History\LinkEntity::$DBF
			)
		));

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

	#[Console\Meta\Command('list')]
	#[Console\Meta\Info('List the jobs in the directory.')]
	#[Console\Meta\Option('--full', 'show the full filepath')]
	public function
	HandleListJobs():
	int {

		$OptFull = $this->GetOption('full');

		////////

		$Indexer = Common\Filesystem\Indexer::FromPath($this->JobRoot);
		$Jobs = $Indexer->ToDatastore();

		$Jobs->Filter(
			fn(string $Path)
			=> str_ends_with(strtolower($Path), '.json')
		);

		////////

		if(!$OptFull)
		$Jobs->Remap(
			fn(string $Path)
			=> str_replace('.json', '', basename($Path))
		);

		$this->PrintAppHeader('Available Jobs');
		$this->PrintBulletList($Jobs);

		return 0;
	}

	#[Console\Meta\Command('links')]
	#[Console\Meta\Info('Browse/Manage the Link History table.')]
	#[Console\Meta\Value('--delete', 'Delete row by ID')]
	public function
	HandleLinkDB():
	int {

		$this->PrintAppHeader('Link DB');

		////////

		$Links = new History\Links($this);
		$OptDelID = $this->GetOption('delete');

		if($OptDelID)
		$Links->DeleteByID($OptDelID);

		////////

		$Head = [
			'ID', 'Date', 'Job', 'Status', 'URL'
		];

		$Rows = $Links->Find(1);

		$Rows->Remap(fn(History\LinkEntity $Row)=> [
			$Row->ID,
			Common\Date::FromTime($Row->TimeAdded),
			$Row->Job,
			$Row->Status,
			$Row->URL
		]);

		////////

		$this->PrintTable($Head, $Rows->GetData());

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

			if(str_contains($File, "phelium{$DS}"))
			return FALSE;

			if(str_contains($File, "symfony{$DS}console"))
			return FALSE;

			////////

			return TRUE;
		});

		return $Filters;
	}

};
