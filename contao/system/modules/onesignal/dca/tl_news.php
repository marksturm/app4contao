<?php

/**
 * @package		OneSiginal
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

array_insert($GLOBALS['TL_DCA']['tl_news']['config']['onsubmit_callback'],0,array(
	array('tl_news_OneSiginal','preparetoqueue')
	));


class tl_news_OneSiginal extends Backend {
		
		public function preparetoqueue(DataContainer $dc) {
			$source="news";
			$archiv=$dc->activeRecord->pid;
			$content=$dc->activeRecord->id;
			$start=$dc->activeRecord->start;
			$published=$dc->activeRecord->published;


			return OneSignal::addtoqueue($source,$content,$archiv,$published,$start);
	}
}