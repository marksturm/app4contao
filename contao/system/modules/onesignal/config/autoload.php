<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'OneSignal'       => 'system/modules/onesignal/classes/OneSignal.php',

	// Modules
	'ModuleOneSignal' => 'system/modules/onesignal/modules/ModuleOneSignal.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_OneSignal' => 'system/modules/onesignal/templates',
));
