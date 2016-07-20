<?php

/**
 * @package		OneSiginal
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

$GLOBALS['TL_DCA']['tl_onesignal_queue'] = array
(

	// Config
	'config' => array
	(
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'source'=> array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'archive' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'content' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'start' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'sent' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
	)
);