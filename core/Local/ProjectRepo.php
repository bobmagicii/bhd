<?php

namespace Local;

use Nether\Common;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ProjectRepo
extends Common\Prototype {

	public string
	$Path;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetBackupCommand(Project $Project, string $DestRoot):
	string {

		if(str_contains($this->Path, '@'))
		return $this->GetBackupCommandSSH($Project, $DestRoot);

		return '';
	}

	public function
	GetBackupCommandSSH(Project $Project, string $DestRoot):
	string {

		$Bits = Common\Datastore::FromString($this->Path, ':');
		$Paths = Common\Datastore::FromString($Bits[1], '/');
		$Name = $Paths[$Paths->GetLastKey()];

		if(str_ends_with($Name, '.git'))
		$Name = preg_replace('/\.git$/', '', $Name);

		$Path = Common\Filesystem\Util::Pathify($DestRoot, $Name);

		// if we are revisiting a repo pull the updates.

		if(file_exists($Path))
		return sprintf(
			'git -C %s pull -q',
			escapeshellarg($Path)
		);

		// otherwise fresh clone time.

		return sprintf(
			'git clone -q %s %s',
			escapeshellarg($this->Path),
			escapeshellarg($Path)
		);
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
