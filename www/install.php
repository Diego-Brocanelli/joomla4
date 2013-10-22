<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Maximum error reporting for the installer (not something we'd use on the site itself).
error_reporting(-1);
ini_set('display_errors', 1);

// Check for fatal blockers.
if (version_compare(PHP_VERSION, '5.3.1', '<'))
{
	die('Your host needs to use PHP 5.3.1 or higher to run this version of Joomla!');
}

// TODO - We should be able to remove the _JEXEC check if we lock down the web root folders.
define('_JEXEC', true);

// TODO - This is a bit ugly, and hopefully temporary. You should be able to set your PHP file root "somehow".
define('VENDOR', realpath(__DIR__ . '/../vendor'));

// TODO - We need a good way to find the configuration file. Once we have that, we should be able to compute any other path we need.
define('APPLICATION_CONFIG', realpath(dirname(VENDOR) . '/etc/configuration.php'));

// If the configuration file is set, bounce the request
if (file_exists(APPLICATION_CONFIG))
{
	// Set the server response code.
	header('Status: 500', true, 500);

	// There is no need to remove the installation application anymore.
	// TODO - Add a link to a help page on what this error means.
	echo 'Joomla! is already installed.';
	exit;
}

// Fallback exception handling.
try
{
	// Load Composer's standard autoloader.
	require VENDOR . '/autoload.php';

	// RFC - An application has a special `include.php` file which is all you need to include. It's responsible for doing the rest.
	require VENDOR . '/joomla-cms/cms-install-application/src/include.php';
}
catch (Exception $e)
{
	// Set the server response code.
	header('Status: ' . $e->getCode(), true, $e->getCode());

	// An exception has been caught, echo the message and exit.
	echo $e->getMessage();
	exit;
}
