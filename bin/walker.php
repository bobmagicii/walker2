<?php

list($AppRoot, $BootRoot) = (function(){
	if(Phar::Running(FALSE) !== '')
	return [ dirname(Phar::Running(FALSE)), Phar::Running(TRUE) ];

	return array_fill(0, 2, dirname(__FILE__, 2));
})();

require(sprintf('%s/vendor/autoload.php', $BootRoot));

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

use Nether\Browser;
use Nether\Common;
use Nether\Console;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

#[Console\Meta\Application('Walker', '2.0.0-dev', Phar: 'walker.phar')]
class App
extends Nether\Console\Client {

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

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady():
	void {

		$this->Config = new Common\Datastore;
		$this->Library = new Common\Datastore;

		($this->Library)
		->Shove('Browser', new Browser\Library($this->Config));

		////////

		$this->AppRoot = $this->GetOption('AppRoot');
		$this->BootRoot = $this->GetOption('BootRoot');

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

		$Job = new Walker\JobFile;
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
			$Job = Walker\JobFile::FromPath($Filename);
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
			$Job = Walker\JobFile::FromPath($Filename);
		}

		catch(Exception $Error) {
			$this->Quit(2, $Error->GetMessage());
		}

		$Runner = new Walker\JobRunner;
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

			////////

			return TRUE;
		});

		return $Filters;
	}

};

exit(App::Realboot([
	'AppRoot'  => Common\Filesystem\Util::Repath($AppRoot),
	'BootRoot' => $BootRoot
]));
