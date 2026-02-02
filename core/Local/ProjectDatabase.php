<?php

namespace Local;

use Nether\Common;
use Nether\Database;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class ProjectDatabase
extends Common\Prototype {

	public ?string
	$Type = NULL;

	public ?string
	$TunnelHost = NULL;

	public ?string
	$Hostname = NULL;

	public ?string
	$Database = NULL;

	public ?string
	$Username = NULL;

	public ?string
	$Password = NULL;

	public ?string
	$Charset = NULL;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	GetBackupCommand(Project $Project, string $DestRoot):
	string {

		$Outfile = Common\Filesystem\Util::Pathify(
			$DestRoot,
			"{$this->Database}.{$this->Type}.sql"
		);

		////////

		$Cmd = sprintf(
			'mysqldump %s %s %s %s %s',
			escapeshellarg("-h{$this->Hostname}"),
			escapeshellarg("-u{$this->Username}"),
			escapeshellarg("-p{$this->Password}"),
			escapeshellarg($this->Database),
			sprintf('--ignore-table=\'%s.%s\'', $this->Database, 'TrafficRows')
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
	New(	?string $TunnelHost=NULL, ?string $Type=NULL, ?string $Database=NULL, ?string $Hostname=NULL, ?string $Username=NULL, ?string $Password=NULL, ?string $Charset=NULL):
	static {

		$Output = new static([
			'Type'       => $Type,
			'TunnelHost' => $TunnelHost,
			'Hostname'   => $Hostname,
			'Database'   => $Database,
			'Username'   => $Username,
			'Password'   => $Password,
			'Charset'    => $Charset
		]);

		return $Output;
	}

};
