<?php

/**
 * @package		OneSiginal
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

$GLOBALS['TL_DCA']['tl_module']['palettes']['onesignal'] = '{title_legend},name,type;{onesignal_legend},app_id,safari_web_id';

$GLOBALS['TL_DCA']['tl_module']['fields']['app_id']  = array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['app_id'],
			'inputType' => 'text',
			'exclude'   => true,
			'sorting'   => true,
			'flag'      => 1,
			'search'    => true,
			'eval'      => array(
				'mandatory' => true,
				'unique'    => false,
				'maxlength' => 255,
				'tl_class'  => 'w50'
			),
			'sql'       => "varchar(255) NOT NULL default ''"
		);

$GLOBALS['TL_DCA']['tl_module']['fields']['safari_web_id']  = array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_module']['safari_web_id'],
			'inputType' => 'text',
			'exclude'   => true,
			'sorting'   => true,
			'flag'      => 1,
			'search'    => true,
			'eval'      => array(
				'mandatory' => true,
				'unique'    => false,
				'maxlength' => 255,
				'tl_class'  => 'w50'
			),
			'sql'       => "varchar(255) NOT NULL default ''"
		);