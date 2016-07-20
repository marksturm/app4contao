<?php

/**
 * @package		OneSiginal
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

class OneSignal
{
	public function addtoqueue($source,$content,$archive,$published,$start){
	

		switch ($source)
			{
			case "news":
				$objCheckArchivForOneSignal = Database::getInstance()->prepare("SELECT onesignal FROM tl_news_archive WHERE id=?")->execute($archive)->onesignal;
			break;
			case "calendar":
				$objCheckArchivForOneSignal = Database::getInstance()->prepare("SELECT onesignal FROM tl_calendar WHERE id=?")->execute($archive)->onesignal;
			break;
			}

		if($published && $objCheckArchivForOneSignal) {

			$objCheckQueue = Database::getInstance()->prepare("SELECT id FROM tl_onesignal_queue WHERE content=? AND source=?")->execute($content,$source);
			
			$setQueue = array('tstamp' => time(), 'source' => $source, 'content' => $content, 'start' => $start, 'archive' => $archive);

			if ($objCheckQueue->numRows < 1) {
			
				$objQueueInsert = Database::getInstance()->prepare("INSERT INTO tl_onesignal_queue %s")->set($setQueue)->execute();
	       	
	       	} else {

	    		$objQueueUpdate = Database::getInstance()->prepare("UPDATE tl_onesignal_queue %s WHERE content=? AND source=?")->set($setQueue)->execute($content,$source);

	        }
	    }

	    //\OneSignal::RunOneSignalQueue();
    }

	public function getContentData($source,$content) {

		switch ($source)
		{
		case "news":
			$str_headline= \Controller::replaceInsertTags('{{news_title::'.$content.'}}');
			$str_url= \Controller::replaceInsertTags('{{news_url::'.$content.'}}');
		break;
		case "calendar":
			$str_headline = \Controller::replaceInsertTags('{{event_title::'.$content.'}}');
			$str_url= \Controller::replaceInsertTags('{{event_url::'.$content.'}}');
		break;
		}
		$object = (object) ['headline' => $str_headline, 'url' => $str_url];
		return $object;
	}

	public function RunOneSignalQueue(){

		$objOneSignalQueue = Database::getInstance()->prepare("SELECT * FROM tl_onesignal_queue WHERE start <= ?")->execute(time());

		while($objOneSignalQueue->next())
		{ 
			switch ($objOneSignalQueue->source)
			{
			case "news":				
				$objForOneSignal = Database::getInstance()->prepare("SELECT onesignal FROM tl_news_archive WHERE id=?")->execute($objOneSignalQueue->archive);
			break;
			case "calendar":
				$objForOneSignal = Database::getInstance()->prepare("SELECT onesignal FROM tl_calendar WHERE id=?")->execute($objOneSignalQueue->archive);
			break;
			}    	
			
			while($objForOneSignal->next()) {
				
				foreach(unserialize($objForOneSignal->onesignal) AS $OneSignalId) {
						
					$objSettingsOneSignal = Database::getInstance()->prepare("SELECT * FROM tl_onesignal WHERE id=?")->execute($OneSignalId);

						if($objSettingsOneSignal->useSSL) {
								$str_http = "https://";
							} else {
								$str_http = "http://";
							}

						$objContent=\OneSignal::getContentData($objOneSignalQueue->source,$objOneSignalQueue->content);
						$str_url=$str_http.$objSettingsOneSignal->dns.'/'.$objContent->url;

						OneSignal::sendOneSignal($objContent->headline,$objSettingsOneSignal->app_id,$objSettingsOneSignal->authcode,$str_url,'browser');

						OneSignal::sendOneSignal($objContent->headline,$objSettingsOneSignal->app_id,$objSettingsOneSignal->authcode,$str_url,'app');

					 	Database::getInstance()->prepare("DELETE FROM tl_onesignal_queue WHERE content=?")->execute($objOneSignalQueue->content);
				}
			}
		}
	}
   
	public function sendOneSignal($str_text,$str_app_id, $str_auth_code,$str_url,$var_receiver){

		$content = array(
			"en" => html_entity_decode($str_text),
		);

		switch ($var_receiver)
			{
	    		case "browser":
						$fields = array(
							'app_id' => $str_app_id,
							'included_segments' => array('All'),
							'contents' => $content,
							'isAndroid' => false,
							'isIos' => false,
							'isAnyWeb' => true,
							'url' => $str_url
						);
	    		break;
	    		case "app":
						$fields = array(
							'app_id' => $str_app_id,
							'included_segments' => array('All'),
							'contents' => $content,
							'isAndroid' => true,
							'isIos' => true,
							'isAnyWeb' => true,
							'url' => $str_url
						);
	    		break;
			}

	    $fields = json_encode($fields);
	    
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$str_auth_code.''));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    return $response;	    
  	}

}
