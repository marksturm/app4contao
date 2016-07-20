<?php

/**
 * Front end modules
 */

$GLOBALS['FE_MOD']['miscellaneous']['onesignal'] = 'ModuleOneSignal';

$GLOBALS['BE_MOD']['system']['onesignal'] = array(
		'tables' => array('tl_onesignal'),
		'icon'   => 'system/modules/onesignal/assets/onesignal.png'
	);

/**
 * Cron Job
 */
$GLOBALS['TL_CRON']['hourly'][] = array('OneSignal', 'RunOneSignalQueue');
