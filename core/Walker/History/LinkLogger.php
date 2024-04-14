<?php

namespace Walker\History;

class LinkLogger {

	public function
	__Construct() {

		//$H = $this->DB->Get('History');
		//$H->Query('CREATE TABLE `HistoryLink` (`ID` INT UNSIGNED, `URL` VARCHAR(255), `Status` VARCHAR(16), `ExtraJSON` TEXT);');
		//Common\Dump::Var($H->Query('SELECT * FROM `HistoryLink`;')->Glomp());

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

};

