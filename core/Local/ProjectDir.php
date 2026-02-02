<?php

namespace Local;

use Nether\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ProjectDir
extends Common\Prototype {

	public string
	$Path;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetBackupCommand(Project $Project, string $DestRoot):
	string {

		// the -a argument seems to cause issues on mac
		// due to its inclusion of p, g, and o, causing
		// infinite hangs of getting nothing done at random
		// times.

		return sprintf(
			//'rsync -azq --delete %s %s',
			'rsync -rltDzq --delete %s %s',
			escapeshellarg($this->Path),
			escapeshellarg($DestRoot)
		);
	}

	public function
	IsLocalPath():
	bool {

		if(!str_contains($this->Path, ':'))
		return TRUE;

		//$Bits = Common\Datastore::FromString($this->Path, ':');
		//Common\Dump::Var($Bits);

		return FALSE;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	New(string $Path):
	static {

		$Output = new static([
			'Path' => $Path
		]);

		return $Output;
	}

};
