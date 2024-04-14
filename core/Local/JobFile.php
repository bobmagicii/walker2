<?php

namespace Local;

use Nether\Common;
use Nether\Console;

class JobFile
extends Common\Prototype
implements
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	use
	Common\Package\ToJSON;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Common\Meta\Info('Where this file is save/loaded.')]
	public ?string
	$Filename = NULL;

	#[Common\Meta\Info('Title to display for the job when doing things.')]
	#[Common\Meta\PropertyListable]
	public string
	$Title = 'Untitled Job';

	#[Common\Meta\Info('Selector for finding the objects we want to inspect.')]
	#[Common\Meta\PropertyListable]
	#[Common\Meta\PropertyFactory('FromArray')]
	public array|Common\Datastore
	$Steps = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	OnReady(Common\Prototype\ConstructArgs $Args):
	void {

		$this->Steps->Remap(function(string|array $In) {
			if(is_string($In))
			return new JobSeeker([ 'Class'=> $In ]);

			return new JobSeeker($In);
		});

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetFilename():
	?string {

		return $this->Filename;
	}

	public function
	ToArray():
	array {

		$Output = [ ];

		////////

		$Props = Common\Meta\PropertyListable::FromClass(static::class);
		$Prop = NULL;

		foreach($Props as $Prop) {
			/** @var Common\Prototype\PropertyInfo $Prop */

			$Output[$Prop->Name] = match(TRUE) {

				($this->{$Prop->Name} instanceof Common\Datastore)
				=> ($this->{$Prop->Name})->GetData(),

				default
				=> $this->{$Prop->Name}

			};
		};

		////////

		return $Output;
	}

	public function
	Write():
	void {

		if(!$this->Filename)
		throw new Common\Error\RequiredDataMissing('Filename', 'string');

		////////

		if(!is_dir(dirname($this->Filename)))
		Common\Filesystem\Util::MkDir(dirname($this->Filename));

		if(!is_dir(dirname($this->Filename)))
		throw new Common\Error\DirNotFound($this->Filename);

		////////

		file_put_contents(
			$this->Filename,
			Common\Filters\Text::Tabbify(json_encode(
				$this->ToArray(),
				(JSON_PRETTY_PRINT)
			))
		);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromPath(string $Filename):
	static {

		$Data = Common\Filesystem\Util::TryToReadFileJSON($Filename);

		if(!is_array($Data))
		throw new Common\Error\FormatInvalid;

		////////

		$Output = new static(array_merge(
			$Data,
			[ 'Filename'=> $Filename ]
		));

		return $Output;
	}

	static public function
	DebugLn(string $Msg):
	void {

		Console\Util::PrintLn("[JobFile] {$Msg}");

		return;
	}

};
