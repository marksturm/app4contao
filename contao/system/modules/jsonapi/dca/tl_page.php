<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

$GLOBALS['TL_DCA']['tl_page']['palettes']['JsonApi']="{title_legend},title,alias,type;{imgSize_legend},imgSize";

	$GLOBALS['TL_DCA']['tl_page']['fields']['imgSize'] = array
	(
			'label'                   => &$GLOBALS['TL_LANG']['tl_page']['imgSize'],
			'exclude'                 => true,
			'inputType'               => 'imageSize',
			'options'                 => System::getImageSizes(),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
	);