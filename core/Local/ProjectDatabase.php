<?php

namespace Local;

use Nether\Common;
use Nether\Database;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ProjectDatabase
extends Common\Prototype {

	public string
	$Alias;

	public ?string
	$TunnelHost = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetBackupCommand(Project $Project, string $DestRoot):
	string {

		$DM = new Database\Manager;
		$DB = $DM->Get($this->Alias);

		$Outfile = Common\Filesystem\Util::Pathify(
			$DestRoot,
			"{$DB->Name}.sql"
		);

		////////

		$Cmd = sprintf(
			'mysqldump %s %s %s %s',
			escapeshellarg("-h{$DB->Hostname}"),
			escapeshellarg("-u{$DB->Username}"),
			escapeshellarg("-p{$DB->Password}"),
			escapeshellarg($DB->Database)
		);

		// tunnel it through ssh if needed.

		if($this->TunnelHost)
		$Cmd = sprintf(
			'ssh %s "%s"',
			escapeshellarg($this->TunnelHost),
			$Cmd
		);

		// and write it.

		$Cmd .= sprintf(
			' > %s',
			escapeshellarg($Outfile)
		);

		return $Cmd;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	static public function
	New(string $Alias, ?string $TunnelHost=NULL):
	static {

		$Output = new static([
			'Alias'      => $Alias,
			'TunnelHost' => $TunnelHost
		]);

		return $Output;
	}

};
