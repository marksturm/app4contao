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
	'ContaoJsonApi'         => 'system/modules/jsonapi/classes/ContaoJsonApi.php',
	'ContaoJsonApiElements' => 'system/modules/jsonapi/classes/ContaoJsonApiElements.php',
	'ContaoJsonApiHelper'   => 'system/modules/jsonapi/classes/ContaoJsonApiHelper.php',
	'ContaoJsonApiNewsList' => 'system/modules/jsonapi/classes/ContaoJsonApiNewsList.php',
));
