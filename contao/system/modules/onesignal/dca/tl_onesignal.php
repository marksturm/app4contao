<?php

/**
 * @package		OneSiginal
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

$GLOBALS['TL_DCA']['tl_onesignal'] = array
(
	'config'   => array
	(
		'dataContainer'    => 'Table',
		'enableVersioning' => true,
		'sql'              => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		),
	),
	'list'     => array
	(
		'sorting'           => array
		(
			'mode'                    => 1,
			'fields'                  => array('id DESC'),
			'flag'                    => 1,
			'panelLayout'             => 'limit'
		),
		'label' => array
		(
			'fields'                  => array('dns','app'),
			'format'                  => '<span style="color:#b3b3b3; padding-right:3px;">[%s]</span> %s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations'        => array
		(
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_onesignal']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_onesignal']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show'   => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_onesignal']['show'],
				'href'       => 'act=show',
				'icon'       => 'show.gif',
				'attributes' => 'style="margin-right:3px"'
			),

		)
	),

	'palettes' => array
	(
		'default'       => 'app,dns,useSSL;{settings_legend},app_id,authcode',
	),

	'fields'   => array
	(
		'id'     => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'app' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onesignal']['app'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255,'tl_class'=>'long'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'dns' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onesignal']['dns'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'search'                  => true,
			'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'save_callback' => array
			(
				array('tl_onesignal', 'checkDns')
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'useSSL' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onesignal']['useSSL'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'app_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onesignal']['app_id'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'authcode' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_onesignal']['authcode'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		)
	)
);

class tl_onesignal {
	public function checkDns($varValue)
	{
		return str_ireplace(array('http://', 'https://', 'ftp://', '/'), '', $varValue);
	}
}
