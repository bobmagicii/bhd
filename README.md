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

This is done via:

* `rsync -azq --delete` meaning quietly archive, compress the network, and
   delete files in the destination that have been removed from the source. On
   a snapshot that means nothing but on a living backup it means it is kept in
   sync with the source.

## Repositories

Add a new git repository to clone over here.

* `$ bhd repo <project> <repo-url>`
* `$ bhd repo example git@example.tld:/path/to/repo`

This is done via:

* `git clone repo-url` the clone the repo as it is now.
* `git pull -C path` in future runs on living backups.

## Databases

Add a new database to back up over here. First configure the database connection. Note that if intending to use an SSH tunnel the host you specify to `project.atl` may be something like `localhost` rather than the FQDN.

* `$ project.atl db ...`
* `$ project.atl db --set=exampledb --type=mysql --host=example.tld --user=exuser --pass=expass --db=exdbname`

Then add the database to the backup tool.

* `$ bhd db <project> <alias>`
* `$ bhd db example exampledb`

This is done via:

* `mysqldump ... > backup.sql` directly connecting and exporting the DB.
* `ssh tunnel-host "mysqldump ..." > backup.sql` if TunnelHost is set.



# Running The Backup

Perform all the actions configured for this project.

* `$ bhd run example`



# Install

* `$ git clone https://github.com/bobmagicii/bhd`
* `$ cd bhd`
* `$ composer install`



# Update

* `$ git pull && composer install`



# Command Help

* `$ bhd` to list all commands.
* `$ bhd help <cmd>` to see details about one command.
* Note: if not PATHed then you need to run `$ php bin/bhd`.



# Other Information

* Project files are stored in `data/projects`

* Backups are stored in `data/backups`

* The CLI commands provided such as `backup` and `project.atl` working as they are shown require adding `./vendor/bin` and `./bin` to PATH.



# Todo

* This is sitting on top of my entire web stack so add a little self hosted dashboard for management.


