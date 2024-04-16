<?php

namespace Walker\History;

use Walker;
use Nether\Common;
use Nether\Database;

class Links {

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

		return;
	}

	public function
	Has(string $Job, string $URL, ?string $Status=NULL):
	bool {

		return FALSE;
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

		$DB = $this->App->DB->Get('History');

		if($this->DoesTableExist($DB, 'HistoryLink'))
		return;

		$DB->Query(<<< SQL
			CREATE TABLE `HistoryLink` (
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
		$Row->Drop();

		return;
	}

};

