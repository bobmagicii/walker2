<?php

namespace Walker\History;

use Nether\Common;
use Nether\Database;

#[Database\Meta\TableClass('HistoryLink')]
class LinkEntity
extends Database\Prototype {

	static public string
	$DBA = 'HistoryLink';

	static public string
	$DBF = 'history-link.sqlite';

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	#[Database\Meta\TypeIntBig(Unsigned: TRUE, AutoInc: TRUE)]
	#[Database\Meta\PrimaryKey]
	public int
	$ID;

	#[Database\Meta\TypeVarChar(Size: 36)]
	public string
	$UUID;

	#[Database\Meta\TypeIntBig(Unsigned: TRUE)]
	public int
	$TimeAdded;

	#[Database\Meta\TypeVarChar(Size: 255)]
	public string
	$Job;

	#[Database\Meta\TypeVarChar(Size: 255)]
	public string
	$URL;

	#[Database\Meta\TypeVarChar(Size: 16)]
	public string
	$Status;

	#[Database\Meta\TypeText]
	public string
	$ExtraJSON;

};

