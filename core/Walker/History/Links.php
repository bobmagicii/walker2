<?php

namespace Walker\History;

use Walker;
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
	Add(string $URL, string $Job='any', string $Status='added'):
	void {

		return;
	}

	public function
	Has(string $URL, string $Job='any'):
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

		if(!$this->DoesTableExist($DB, 'HistoryLink'))
		$DB->Query(<<< SQL
			CREATE TABLE `HistoryLink` (
				`ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				`UUID` CHAR(36),
				`URL` VARCHAR(255),
				`Status` VARCHAR(16),
				`ExtraJSON` TEXT
			);
		SQL);

		return;
	}

};

