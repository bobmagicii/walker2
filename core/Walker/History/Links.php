<?php

namespace Walker\History;

use Walker;
use Nether\Common;
use Nether\Database;

class Links {

	const
	SortNew = 'new',
	SortOld = 'old';

	////////

	protected Walker\TerminalApp
	$App;

	public function
	__Construct(Walker\TerminalApp $App) {

		$this->App = $App;
		$this->InstallHistoryLinkTable();

		return;
	}

	public function
	Add(string $Job, string $URL, string $Status='added', ?iterable $Extra=NULL):
	void {

		// prototype's inserts from mysql seem to be working with sqlite.

		Walker\History\LinkEntity::Insert([
			'Job'       => $Job,
			'URL'       => $URL,
			'Status'    => $Status,
			'ExtraJSON' => json_encode($Extra)
		]);

		return;
	}

	public function
	Has(?string $Job=NULL, ?string $URL=NULL, ?string $Status=NULL):
	bool {

		$Rows = $this->Find(
			Limit: 1,
			Job: $Job,
			URL: $URL,
			Status: $Status
		);

		return $Rows->Count() > 0;
	}

	public function
	Find(int $Page=1, int $Limit=20, ?string $Job=NULL, ?string $URL=NULL, ?string $Status=NULL, string $Sort=self::SortNew):
	Common\Datastore {

		// this would normally be handled by the Find() method on the
		// link entity class if that worked on sqlite.

		$DB = $this->App->DB->Get(LinkEntity::class);
		$Table = LinkEntity::GetTableInfo();
		$SQL = $DB->NewVerse();
		$Dataset = [];
		$Offset = (Common\Filters\Numbers::Page($Page) - 1) * $Limit;

		////////

		$SQL->Select($Table->Name);
		$SQL->Fields('*');
		$SQL->Offset($Offset);
		$SQL->Limit($Limit);

		////////

		if($Job !== NULL) {
			$Dataset[':Job'] = $Job;
			$SQL->Where('Job=:Job');
		}

		if($URL !== NULL) {
			$Dataset[':URL'] = $URL;
			$SQL->Where('URL=:URL');
		}

		if($Status !== NULL) {
			$Dataset[':Status'] = $Status;
			$SQL->Where('Status=:Status');
		}

		////////

		switch($Sort) {
			case static::SortNew:
				$SQL->Sort('TimeAdded', $SQL::SortDesc);
				break;
			case static::SortOld:
				$SQL->Sort('TimeAdded', $SQL::SortAsc);
				break;
		}

		////////

		$Result = (
			Common\Datastore::FromArray($DB->Query($SQL, $Dataset)->Glomp())
			->Remap(fn(object $Row)=> new LinkEntity($Row))
		);

		return $Result;
	}

	public function
	DeleteByID(int $ID):
	void {

		$DB = $this->App->DB->Get(LinkEntity::class);
		$Table = LinkEntity::GetTableInfo();
		$SQL = $DB->NewVerse();

		$SQL->Delete($Table->Name);
		$SQL->Where('ID=:ID');

		$DB->Query($SQL, [ ':ID'=> $ID ]);

		return;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	DoesTableExist(Database\Connection $DB, string $Name):
	bool {

		$SQL =<<< SQL
			SELECT `name` FROM `sqlite_master`
			WHERE `type`="table" AND `name`=:Name LIMIT 1;
		SQL;

		$Check = $DB->Query($SQL, [ ':Name'=> $Name ]);
		$Found = $Check->Next() !== FALSE;

		////////

		return $Found;
	}

	protected function
	InstallHistoryLinkTable():
	void {

		$DBA = Walker\History\LinkEntity::$DBA;
		$DBF = Walker\History\LinkEntity::$DBF;
		$DBT = Walker\History\LinkEntity::GetTableInfo();

		////////

		$DB = $this->App->DB->Get(Walker\History\LinkEntity::$DBA);

		if($this->DoesTableExist($DB, $DBT->Name)) {
			// throw exception for the step to catch and ignore.
			return;
		}

		$DB->Query(<<< SQL
			CREATE TABLE {$DBT->Name} (
				`ID` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
				`UUID` CHAR(36),
				`TimeAdded` INT UNSIGNED,
				`Job` VARCHAR(255),
				`URL` VARCHAR(255),
				`Status` VARCHAR(16) DEFAULT NULL,
				`ExtraJSON` TEXT DEFAULT NULL
			);
		SQL);

		$Row = LinkEntity::Insert([
			'UUID'      => Common\UUID::V7(),
			'TimeAdded' => time(),
			'Job'       => 'none',
			'URL'       => 'https://google.com',
			'Status'    => 'test',
			'ExtraJSON' => ''
		]);

		Common\Dump::Var($Row);
		//$Row->Drop();

		return;
	}

};

