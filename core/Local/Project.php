<?php

namespace Local;

use Nether\Common;
use Nether\Database;
use Exception;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

class Project
extends Common\Prototype
implements
	Common\Interfaces\ToArray,
	Common\Interfaces\ToJSON {

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	const
	StatusNever = 0,
	StatusOK    = 1,
	StatusStale = 2;

	const
	StaleNever = 'never',
	StaleWeek  = '1 week';

	const
	TypeDateTime = 'datetime',
	TypeSingle   = 'single';

	const
	Types = [
		self::TypeDateTime,
		self::TypeSingle
	];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	use
	Common\Package\ToJSON;

	public string
	$Filename;

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public string
	$Type = 'datetime';

	public ?string
	$DateLastRun = NULL;

	public ?string
	$StaleAfter = self::StaleWeek;

	#[Common\Meta\PropertyFactory('FromArray')]
	public array|Common\Datastore
	$Dirs = [];

	#[Common\Meta\PropertyFactory('FromArray')]
	public array|Common\Datastore
	$Repos = [];

	#[Common\Meta\PropertyFactory('FromArray')]
	public array|Common\Datastore
	$Databases = [];

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	ToArray():
	array {

		$Output = [
			'DateLastRun' => $this->DateLastRun,
			'StaleAfter'  => $this->StaleAfter,
			'Type'        => $this->Type,
			'Dirs'        => $this->Dirs->ToArray(),
			'Repos'       => $this->Repos->ToArray(),
			'Databases'   => $this->Databases->ToArray()
		];

		return $Output;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	UpdateLastRun(Common\Date $When):
	static {

		$this->DateLastRun = $When->Get(
			Common\Values::DateFormatYMDT24VO
		);

		return $this;
	}

	public function
	Read():
	static {

		$Data = Common\Datastore::FromArray(
			Common\Filesystem\Util::TryToReadFileJSON($this->Filename)
		);

		////////

		if(isset($Data['Type']))
		$this->Type = $Data['Type'];

		if(isset($Data['DateLastRun']))
		$this->DateLastRun = $Data['DateLastRun'];

		if(isset($Data['StaleAfter']))
		$this->StaleAfter = $Data['StaleAfter'];

		if(isset($Data['Dirs'])) {
			($this->Dirs)
			->Import($Data['Dirs'])
			->Remap(fn(array $D)=> new ProjectDir($D));
		}

		if(isset($Data['Repos'])) {
			($this->Repos)
			->Import($Data['Repos'])
			->Remap(fn(array $R)=> new ProjectRepo($R));
		}

		if(isset($Data['Databases'])) {
			($this->Databases)
			->Import($Data['Databases'])
			->Remap(fn(array $D)=> new ProjectDatabase($D))
			->Each(function(ProjectDatabase $D){

				// port old config to new config.

				if(isset($D->Type) && isset($D->Hostname))
				return;

				$DM = new Database\Manager;
				$DB = $DM->Get($D->Alias);

				if(!$DB)
				return;

				$D->Type = $DB->Type;
				$D->Database = $DB->Database;
				$D->Hostname = $DB->Hostname;
				$D->Username = $DB->Username;
				$D->Password = $DB->Password;
				$D->Charset = $DB->Charset;
				$this->Write();

				return;
			});
		}

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

	public function
	GetName():
	string {

		$Name = basename($this->Filename);
		$Name = str_replace('.json', '', $Name);

		return $Name;
	}

	public function
	GetType():
	string {

		return $this->Type;
	}

	public function
	GetStatus():
	int {

		if(!$this->DateLastRun)
		return static::StatusNever;

		if($this->IsStale())
		return static::StatusStale;

		return static::StatusOK;
	}

	public function
	GetStatusWord():
	string {

		return match($this->GetStatus()) {
			static::StatusNever => 'Never',
			static::StatusStale => 'Stale',
			static::StatusOK    => 'OK',
			default             => 'Unknown'
		};
	}

	public function
	GetTimeSince():
	int {

		$Now = Common\Date::Unixtime();
		$Then = Common\Date::Unixtime($this->DateLastRun);

		return $Now - $Then;
	}

	public function
	GetTimeframeUntil():
	Common\Units\Timeframe {

		if($this->StaleAfter === 'never')
		return new Common\Units\Timeframe('now', 'now');

		if(!$this->DateLastRun)
		return new Common\Units\Timeframe('now', 'now');

		////////

		$Now = Common\Date::FromDateString('now', NULL, TRUE);
		$Then = Common\Date::FromDateString($this->DateLastRun ?? '', NULL, TRUE);
		$When = $Then->Modify($this->StaleAfter);

		$Frame = new Common\Units\Timeframe($Now, $When);
		$Frame->SetFormat($Frame::FormatShorter);

		////////

		return $Frame;
	}

	public function
	GetDateLastRunShort():
	string {

		if(!$this->DateLastRun)
		return '';

		$Date = Common\Date::FromDateString($this->DateLastRun);

		return $Date->Get(Common\Values::DateFormatYMDT24);
	}

	public function
	HasRan():
	bool {

		return $this->DateLastRun !== NULL;
	}

	public function
	IsStale():
	bool {

		if($this->StaleAfter === static::StaleNever)
		return FALSE;

		$Now = Common\Date::Unixtime();
		$Then = Common\Date::FromDateString($this->DateLastRun ?? '', NULL, TRUE);
		$When = $Then->Modify($this->StaleAfter);

		return $Now >= $When->GetUnixtime();
	}

	public function
	HasDirPath(string $Path):
	bool {

		return (
			($this->Dirs)
			->Distill(fn(ProjectDir $D)=> $D->Path === $Path)
			->IsNotEmpty()
		);
	}

	public function
	HasRepoPath(string $Path):
	bool {

		return (
			($this->Repos)
			->Distill(fn(ProjectRepo $D)=> $D->Path === $Path)
			->IsNotEmpty()
		);
	}

	public function
	HasDatabaseAlias(string $DBName):
	bool {

		return (
			($this->Databases)
			->Distill(fn(ProjectDatabase $D)=> $D->Database === $DBName)
			->IsNotEmpty()
		);
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public function
	Run(string $DestRoot):
	Common\Datastore {

		$Err = NULL;
		$Timer = new Common\Timer;
		$Now = new Common\Date;
		$Commands = new Common\Datastore;

		////////

		try {
			$Commands->MergeRight($this->RunDirectories($DestRoot, $Now));
			$Commands->MergeRight($this->RunRepos($DestRoot, $Now));
			$Commands->MergeRight($this->RunDatabases($DestRoot, $Now));
		}

		catch(Exception $Err) {

		}

		////////

		return $Commands;
	}

	protected function
	RunDirectories(string $DestRoot, Common\Date $When):
	Common\Datastore {

		if($this->Dirs->Count() === 0)
		return new Common\Datastore;

		////////

		$Commands = new Common\Datastore;
		$BackupRoot = $this->GetBackupRoot($DestRoot, $When, 'dirs');
		Common\Filesystem\Util::MkDir($BackupRoot);

		////////

		$Key = NULL;
		$Dir = NULL;

		foreach($this->Dirs as $Key => $Dir) {
			$Command = $Dir->GetBackupCommand($this, $BackupRoot);
			$Commands[sprintf('Dir %d', ($Key+1))] = $Command;
		}

		return $Commands;
	}

	protected function
	RunRepos(string $DestRoot, Common\Date $When):
	Common\Datastore {

		if($this->Repos->Count() === 0)
		return new Common\Datastore;

		////////

		$Commands = new Common\Datastore;
		$BackupRoot = $this->GetBackupRoot($DestRoot, $When, 'repos');
		Common\Filesystem\Util::MkDir($BackupRoot);

		////////

		$Key = NULL;
		$Dir = NULL;

		foreach($this->Repos as $Key => $Dir) {
			$Command = $Dir->GetBackupCommand($this, $BackupRoot);
			$Commands[sprintf('Repo %d', ($Key+1))] = $Command;
		}

		return $Commands;
	}

	protected function
	RunDatabases(string $DestRoot, Common\Date $When):
	Common\Datastore {

		if($this->Databases->Count() === 0)
		return new Common\Datastore;

		////////

		$Commands = new Common\Datastore;
		$BackupRoot = $this->GetBackupRoot($DestRoot, $When, 'dbs');
		Common\Filesystem\Util::MkDir($BackupRoot);

		////////

		$Key = NULL;
		$Dir = NULL;

		foreach($this->Databases as $Key => $Dir) {
			$Command = $Dir->GetBackupCommand($this, $BackupRoot);
			$Commands[sprintf('DB %d', ($Key+1))] = $Command;
		}

		return $Commands;
	}

	////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	protected function
	GetBackupRoot(string $DestRoot, Common\Date $When, string $Dir):
	string {

		$Output = match($this->Type) {
			static::TypeDateTime
			=> Common\Filesystem\Util::Pathify(
				$DestRoot, $When->Get('Ymd-His'), $Dir
			),

			default
			=> Common\Filesystem\Util::Pathify(
				$DestRoot, $Dir
			)
		};

		return $Output;
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

	static public function
	SorterDateRun(self $A, self $B):
	int {

		if($A->DateLastRun !== $B->DateLastRun)
		return $A->DateLastRun <=> $B->DateLastRun;

		return static::SorterFilename($A, $B);
	}

	static public function
	SorterFilename(self $A, self $B):
	int {

		return $A->Filename <=> $B->Filename;
	}

};
