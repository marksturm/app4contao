<?php

/**
 * @package		OneSiginal
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

	$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace('allowComments','allowComments;{onesignal_legend},onesignal;',$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']);

	$GLOBALS['TL_DCA']['tl_calendar']['fields']['onesignal'] = array
	(
		'label'						=> &$GLOBALS['TL_LANG']['tl_calendar']['onesignal'],
		'exclude'					=> true,
		'inputType'					=> 'checkbox',
		'foreignKey'				=> 'tl_onesignal.app',
		'eval'						=> array('mandatory'=>false, 'multiple'=>true),
		'sql'						=> "blob NULL",
		'relation'					=> array('type'=>'hasMany', 'load'=>'lazy')
	);
?>
