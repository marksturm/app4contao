<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ContaoJsonApiElements
{
	public function Elements($imgSize,$id,$pid,$limit,$page)
	{	
		if(!$limit) {
			$strLimit = 5;
			} else {
			$strLimit = $limit;
		}

		$json_models = implode("','", array('text','headline','image','gallery','list','youtube'));
		
		if($pid) {
			$where = "WHERE type IN ('".$json_models."') AND invisible != 1 AND pid IN (".$pid.") AND tstamp > 0 ORDER BY sorting ASC";
		}
		
		if($id) {
			$where = "WHERE type IN ('".$json_models."') AND invisible != 1 AND id IN (".$id.") AND tstamp > 0 ORDER BY sorting ASC";
		}
		

		$objCount = Database::getInstance()->prepare("SELECT COUNT(id) AS cnt FROM tl_content ".$where)->execute(); 

		$page = $page ? : 1;
		
		$total = $objCount->cnt;
		$pages = ceil($objCount->cnt/$strLimit);

		$limit = $strLimit;
		
		$offset += (max($page, 1) - 1) * $strLimit;
			if ($offset + $limit > $total)
				{
					$limit = $total - $offset;
				}

		$objTable = Database::getInstance()->prepare("SELECT id,type,sorting FROM tl_content ".$where)->limit($limit,$offset)->execute();

		while ($objTable->next())
		{

			switch ($objTable->type) {
				case "headline":
					$arrTable[]= ContaoJsonApiElements::getHeadlineElement($objTable->id);
				break;
				case "text":
					$arrTable[] = ContaoJsonApiElements::getTextElement($objTable->id,$imgSize);
				break;
				case "image":
					$arrTable[]= ContaoJsonApiElements::getImageElement($objTable->id,$imgSize);
				break;
				case "gallery":
					$arrTable[]= ContaoJsonApiElements::getGalleryElement($objTable->id,$imgSize);
				break;
				case "list":
					$arrTable[]= ContaoJsonApiElements::getListElement($objTable->id);
				break;
				case "youtube":
					$arrTable[]= ContaoJsonApiElements::getYoutubeElement($objTable->id);
				break;
			}
		}
		return ContaoJsonApiHelper::jsonOutput($arrTable,$pages,$total);
	}


	public function getHeadlineElement($str_contentId) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = html_entity_decode(unserialize($objTable->headline)['value']);
		
		return $objOutput;
	}
	public function getTextElement($str_contentId,$arr_imgSize) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,text,addImage,singleSRC,caption FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = html_entity_decode(unserialize($objTable->headline)['value']);
		$objOutput['text'] = html_entity_decode($objTable->text);
		if($objTable->addImage) {
			$objOutput['picture'] = ContaoJsonApiHelper::image2json($objTable->singleSRC,html_entity_decode($objTable->caption),$arr_imgSize);
		}
		return $objOutput;
	}
	public function getImageElement($str_contentId,$arr_imgSize) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,singleSRC,caption FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = html_entity_decode(unserialize($objTable->headline)['value']);
		$objOutput['picture'] = ContaoJsonApiHelper::image2json($objTable->singleSRC,html_entity_decode($objTable->caption),$arr_imgSize);
		
		return $objOutput;
	}
	public function getGalleryElement($str_contentId,$arr_imgSize) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,multiSRC FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = html_entity_decode(unserialize($objTable->headline)['value']);
		$objPictures = \FilesModel::findMultipleByUuids(deserialize($objTable->multiSRC)); 
		
		while ( $objPictures->next() ) { 
			$arrTempPictures[] = ContaoJsonApiHelper::image2json($objPictures->uuid,$objTable->caption,$arr_imgSize);
		}
		
		$objOutput['pictures']=$arrTempPictures;
		return $objOutput;
	}
	public function getListElement($str_contentId) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,listtype,listitems FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = html_entity_decode(unserialize($objTable->headline)['value']);
		$objOutput['listtype'] = $objTable->listtype;
		$objOutput['list'] = unserialize($objTable->listitems);
		
		return $objOutput;
	}
	public function getYoutubeElement($str_contentId) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,youtube FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = html_entity_decode(unserialize($objTable->headline)['value']);
		$objOutput['youtube'] = $objTable->youtube;
		
		return $objOutput;
	}

}
?>
