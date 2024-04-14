<?php

namespace Walker;

use Nether\Common;

use JsonSerializable;

class JobStep
extends Common\Prototype
implements
	JsonSerializable,
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	use
	Common\Package\ToJSON,
	Common\Package\JsonSerializableAsToJSON;

	#[Common\Meta\PropertyListable]
	public ?string
	$Class = NULL;

	#[Common\Meta\PropertyFactory('FromArray')]
	#[Common\Meta\PropertyListable]
	public array|Common\Datastore
	$Args = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	ToArray():
	array {

		$Output = [
			'Class' => $this->Class
		];

		if($this->Args->Count())
		$Output['Args'] = $this->Args->GetData();

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	HasClass():
	bool {

		return class_exists($this->Class);
	}

	public function
	NewInstance():
	object {

		$Output = new ($this->Class)(...$this->Args->GetData());

		return $Output;
	}

};
