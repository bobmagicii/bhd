# Backup Here Device

Most of my projects are made up of three components:

* Web application that lives in a directory.
* Database supporting the web application.
* Git repository somewhere.

This was built to back up these scenarios offsite by pulling the resources from their hosts and backing them up in the proverbial here.


## Features

* Supports multiple projects being backed up.
* Maintain a single living backup or a dated snapshot.
* Clone a local or remote directory via `rsync`
* Checkout a git repository via `git`.
* MySQL Backup via `mysqldump` optionally via SSH tunnel.


## Requirements

* PHP 8.1+
* `rsync` for directory backup.
* `git` for repository backup.
* `ssh` for tunneled database backup.
* `mysqldump` for db backup if not `ssh` tunneling to host with it.


## Projects

Start a new backup project. This will create a JSON file named this so it is best to choose simple names as you're going to retype it a lot.

* `$ bhd new <project>`
* `$ bhd new example`


## Directories

Add a new local or remote directory to copy over here.

* `$ bhd dir <project> <dir>`
* `$ bhd dir example example.tld:/path/to/app`


## Repositories

Add a new git repository to clone over here.

* `$ bhd repo <project> <repo-url>`
* `$ bhd repo example git@example.tld:/path/to/repo`


## Databases

Add a new database to back up over here.

* `$ bhd db <project> --db=... --tunnel=... --host=... --user=... --pass=...`
* `$ bhd db example --db=ExampleDB --tunnel=example.tld --user=exuser --pass=expass`

And then to remove it from the config.

* `$ bhd db example --del --db=ExampleDB`


## Running The Backup

Perform all the actions configured for this project.

* `$ bhd run example`

Run all the projects which are due to be updated. This would be what you want to put in your crontab.

* `$ bhd autorun`

<details>
	<summary>How It Works</summary>


* `rsync -azq --delete` meaning quietly archive, compress the network, and delete files in the destination that have been removed from the source. On a snapshot that means nothing but on a living backup it means it is kept in sync with the source.

* `git clone repo-url` the clone the repo as it is now.

* `git pull -C path` in future runs if single living backups mode.

* `mysqldump ... > backup.sql` directly connecting and exporting the DB.

* `ssh tunnel-host "mysqldump ..." > backup.sql` if TunnelHost is set.

Note: The SSH tunnel currently has no auth config in this utility as it is
expecting you have magic key entry configured from here to there.
</details>


## Install

* `$ git clone https://github.com/bobmagicii/bhd`
* `$ cd bhd`
* `$ composer install`


## Update

* `$ git pull && composer install`


## Command Help

* `$ bhd` to list all commands.
* `$ bhd help <cmd>` to see details about one command.
* Note: if not PATHed then you need to run `$ php bin/bhd`.


## Other Information

* Project files are stored in `data/projects`.

* Backups are stored in `data/backups` unless `BackupRoot` is defined in `data/bhd.json`.

* The CLI commands provided such as `backup` and `project.atl` working as shown require adding `./vendor/bin` and `./bin` to PATH.


## Todo

* Move database config to the Project JSON instead, because the `project.atl` stuff is bound by Nether\Database's support for a server type when that just is not needed to spit out CLI commands.

* This is sitting on top of my entire web stack so add a little self hosted dashboard for management.
