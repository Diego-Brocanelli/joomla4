<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// TODO - Try and decouple all these old JPATHs.
define('JPATH_INSTALLATION', __DIR__);
define('JPATH_LIBRARIES', realpath(VENDOR . '/joomla-cms/cms-libraries/src'));
require JPATH_LIBRARIES . '/import.php';

// Bootstrap the application
require_once __DIR__ . '/application/bootstrap.php';

// Get the application
$app = JApplicationWeb::getInstance('InstallationApplicationWeb');

// Execute the application
$app->execute();
