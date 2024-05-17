<?php

namespace Local;

use Nether\Common;

class ConfigFile
extends Common\Prototype
implements
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	use
	Common\Package\ToJSON;

	public ?string
	$Filename = NULL;

	public ?string
	$BackupRoot = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	ToArray():
	array {

		$Output = [
			'BackupRoot' => $this->BackupRoot
		];

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Read():
	static {

		$Data = Common\Datastore::FromArray(
			Common\Filesystem\Util::TryToReadFileJSON($this->Filename)
		);

		////////

		if(isset($Data['BackupRoot']))
		$this->BackupRoot = $Data['BackupRoot'];

		////////

		return $this;
	}

	public function
	Write():
	static {

		if(!isset($this->Filename))
		throw new Common\Error\RequiredDataMissing('Filename', 'path to file');

		Common\Filesystem\Util::TryToWriteFile(
			$this->Filename,
			$this->ToJSON()
		);

		return $this;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	FromFile(string $Filename):
	static {

		$Output = new static([
			'Filename' => $Filename
		]);

		$Output->Read();

		return $Output;
	}

};
