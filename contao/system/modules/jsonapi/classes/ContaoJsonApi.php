<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ContaoJsonApi extends \Frontend

{
	public function generate($objPage) 
    { 
 		if(\Input::get('id')) { 
 				$id = ContaoJsonApiHelper::stupidityCheck('numcom',\Input::get('id'));
 			}
 		if(\Input::get('pid')) { 
				$pid = ContaoJsonApiHelper::stupidityCheck('numcom',\Input::get('pid'));
			}
		if(\Input::get('limit')) {
				$limit = ContaoJsonApiHelper::stupidityCheck('num',\Input::get('limit'));
			}
		if(\Input::get('page')) {
				$page = ContaoJsonApiHelper::stupidityCheck('num',\Input::get('page'));
			}
		
		
		$ptable=\Input::get('ptable');

    	\Controller::setStaticUrls('');
		
		header('Access-Control-Allow-Origin: *');
		header('Content-type: application/json; charset=UTF-8');
		
		switch (\Input::get('modul')) {
	    	case "Element":
	        	echo json_encode(ContaoJsonApiElements::Elements($objPage->imgSize,$id,$pid,$limit,$ptable,$page));
	        break;
	    	case "NewsList":
	       		echo json_encode(ContaoJsonApiNewsList::NewsList($objPage->imgSize,$id,$pid,$limit,$ptable,$page));
	        break;
	    	default:
       		die('FU');
		}

    }

}
?>
