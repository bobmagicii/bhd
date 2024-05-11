<?php

/*//////////////////////////////////////////////////////////////////////////////
// global project settings /////////////////////////////////////////////////////

this file is for main project configuration which should be consistent across
any of the environments it should run on.

////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////*/

/**
 * @var Nether\Common\Datastore $Config
 * @var Nether\Atlantis\Engine $App
 */

use Nether\Atlantis;
use Nether\Avenue;
use Nether\Browser;
use Nether\Common;
use Nether\Email;
use Nether\Storage;
use Nether\User;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// PROJECT CONFIG.

($Config)
->Set(Atlantis\Key::ConfProjectID,        'bhd')
->Set(Atlantis\Key::ConfProjectName,      'Backup Here Device')
->Set(Atlantis\Key::ConfProjectDomain,    'localhost')
->Set(Atlantis\Key::ConfProjectDescShort, 'Little project backup solution.')
->Set(Atlantis\Key::ConfContactTo,        'someone@whatever.tld');

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// FRAMEWORK CONFIG.

($Config)
->Set(Atlantis\Key::ConfLibraries,   [ ])
->Set(Avenue\Library::ConfRouteFile, $App->FromProjectRoot('routes.phson'))
->Set(Avenue\Library::ConfRouteRoot, $App->FromProjectRoot('routes'));

// FRAMEWORK STORAGE API.

($Config)
->Set(Storage\Library::ConfStorageLocations, [
	new Storage\Adaptors\Local(
		Name: 'Default',
		Root: $App->FromProjectRoot('data'),
		URL: '/data/{Path}'
	),
	new Storage\Adaptors\Local(
		Name: 'Temp',
		Root: $App->FromProjectRoot('temp')
	)
]);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// USER CONFIG.

($Config)
->Set(Atlantis\Key::ConfUserAllowLogin, FALSE)
->Set(Atlantis\Key::ConfUserAllowSignup, FALSE)
->Set(Atlantis\Key::ConfDevProdSendOff, 0)
->Set(User\Library::ConfAppleEnabled, FALSE)
->Set(User\Library::ConfAppleID, '')
->Set(User\Library::ConfAppleTeamID, '')
->Set(User\Library::ConfAppleKeyFileID, '')
->Set(User\Library::ConfGitHubEnabled, FALSE)
->Set(User\Library::ConfGitHubID, '')
->Set(User\Library::ConfGitHubSecret, '')
->Set(User\Library::ConfGoogleEnabled, FALSE)
->Set(User\Library::ConfGoogleID, '')
->Set(User\Library::ConfGoogleSecret, '')
->Set(User\Library::ConfDiscordEnabled, FALSE)
->Set(User\Library::ConfDiscordID, '')
->Set(User\Library::ConfDiscordSecret, '');

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// EMAIL CONFIG.

($Config)
->Set(Email\Library::ConfLogFile, $App->FromProjectRoot('logs/{Y}/{M}/email.log'))
->Set(Email\Library::ConfSendGridKey, '')
->Set(Email\Library::ConfMailjetPublicKey, '')
->Set(Email\Library::ConfMailjetPrivateKey, '')
->Set(Email\Library::ConfServerHost, '')
->Set(Email\Library::ConfServerPort, 587)
->Set(Email\Library::ConfServerUsername, '')
->Set(Email\Library::ConfServerPassword, '')
->Set(Email\Library::ConfOutboundVia, NULL)
->Set(Email\Library::ConfOutboundFrom, 'info@whatever.tld')
->Set(Email\Library::ConfOutboundReplyTo, 'who@whatever.tld')
->Set(Email\Library::ConfOutboundName, 'Info');

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// MISC CONFIG.

($Config)
->Set(Common\Date::ConfDefaultTimezone, 'America/Chicago')
->Set(Browser\Key::ConfUserAgent, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0');
